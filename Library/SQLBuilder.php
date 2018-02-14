<?php namespace Library;


use Library\Exceptions\SQLException;

class SQLBuilder {

	private $selects = array();
	private $joins = array();
	private $wheres = array();
	private $orders = array();
	private $groups = array();
	private $searchs = array();
	private $limits = array();
	private $raw_query = '';
	private $sql = false;

	private $model = false;
	private $isCounting = false;
	private $className = '';


	// construct the sql builder
	public function __construct($modelName, $isCounting = false, $selects = array('*'), $joins = array(), $wheres = array(), $searches = array(), $groups = array(), $orders = array(), $limits = array()){
		
		$this->selects = $selects;
		$this->joins = $joins;
		$this->wheres = $wheres;
		$this->orders = $orders;
		$this->groups = $groups;
		$this->limits = $limits;
		$this->searches = $searches;
		$this->isCounting = $isCounting;	
		
		$this->className = $modelName;
		$this->model = new $this->className();
		$this->sql = Database::connection($this->model->connection);
		
	}

	public function all($raw_array = false){
		$this->selects = array('*');
		return $this->get($raw_array);
	}


	// select the specific item 
	public function select($column){

		// is this our default * value? then remove it
		if(count($this->selects) == 1 && $this->selects[0] == '*')
			unset($this->selects[0]); //remove our default value

		if(is_array($column))
			$this->selects = array_merge($this->selects,$column);
		else
			$this->selects[] = $column;

		return $this;
	}

	// join tables together
	public function join($table,$c1, $c2,$type = 'INNER'){
		$this->joins[] = array('type' => $type, 'table' => $table, 'column1' => $c1, 'column2' => $c2);
		return $this;
	}

	// filter the results
	public function where($column,$value,$operator = '='){
		$this->wheres[] = array('column' => $column, 'value' => $value, 'operator' => $operator);
		return $this;
	}

	// columns we want to search on using a specific value
	public function search($column,$value){
		
		if(!isset($this->searches[$value]))
			$this->searches[$value] = array();

		if(is_array($column)){
			foreach($column as $c){
				$this->searches[$value][] = $column;
			}
		} else{
			$this->searches[$value][] = $column;
		}

		return $this;

	}


	// group the results by the following field
	public function groupBy($column){
		$this->groups[] = $column;
		return $this;
	}

	// order the results
	public function orderBy($column,$direction = 'ASC'){
		$this->orders[] = $column.' '.$direction;
		return $this;
	}

	// define a limit ( start and end )
	public function limit($start,$end = false){
		$this->limits[0] = $start;
		if($end !== false){
			$this->limits[1] = $end;
		}
		return $this;
	}

	// we are trying to determine how many physical rows there are
	public function count() {	

		$this->isCounting = true;
		return $this;
	}

	// construct the database query based on results and hit the database with it
	public function get($rawArray = false){

		// load the table we want to work on
		$table = $this->model->table;
		
		// if we are trying to count make sure that that is the only thing we want to return back
		if($this->isCounting){
			$this->selects = array('COUNT(*) AS total');
		}

		// generate a base select string. imploding on each of our select items
		$selectString = 'SELECT '.implode(',',$this->selects);
			
		// figure out if we want to join on any tables
		$joinString = '';
		foreach($this->joins as $join){
			$joinString .= $join['type'].' JOIN '.$join['table'].' ON '.$join['column1'].' = '.$join['column2'];
		}

		// generate the WHERE and AND parts
		// use vsprintf (variable sprintf) to insert them in dynamically
		$whereString = '';
		$index = 0;
		$whereValues = array();
		foreach($this->wheres as $where){
			$assignmentType = isset($this->model->expected[$where['column']]) ? $this->model->expected[$where['column']] : "'%s'";
			$whereString .= $index == 0 ? ' WHERE '.$where['column'].' '.$where['operator'].' '.$assignmentType : ' AND '.$where['column'].' '.$where['operator'].' '.$assignmentType; 
			$whereValues[] = $this->sql->escape_string($where['value']);
			$index++;
		}
		$whereString = $whereValues ? vsprintf($whereString, $whereValues) : '';

		// are we trying to search any fields? Set up the SQL logic for it
		$totalSearches = count($this->searches);
		if($totalSearches){
			$hasWhere = strlen($whereString) ? true : false;
			if($hasWhere){
				if($totalSearches){
					$whereString .= ' AND  (';
				}
			} elseif($totalSearches){
				$whereString = ' WHERE (';
			}

			$index = 0;
			$sindex = $index + 1;
			$breakpoint = $totalSearches - 1;
			$searchValues = array();
			$searchString = '';
			foreach($this->searches as $value => $columns){	
				foreach($columns as $column){
					$searchString .= ' '.$column.' LIKE \'%%%'.$sindex.'$s%%\'';
				}
				$searchValues[] = $this->sql->escape_string($value);

				$index++;
				$sindex++;
			}
			$searchString = vsprintf($searchString, $searchValues);
			$whereString .= $searchString.' )';
		}

		// grouping on any fields
		$groupByString = '';
		if(count($this->groups)){
			$groupByString = 'GROUP BY '.implode(',',$this->groups);
		}

		// how do we want to order the results
		$ordersString = '';
		if(count($this->orders)){
			$ordersString = 'ORDER BY '.implode(',',$this->orders);
		}

		// do we have any limits or positions?
		$limitString = '';
		if(count($this->limits)){
			$limitString = 'LIMIT '.implode(',',$this->limits);
		}

		// create the full string now and then check for errors
		$statement = $selectString.' FROM '.$table.' '.$joinString.' '.$whereString.' '.$groupByString.' '.$ordersString.' '.$limitString;
		$results = $this->sql->query($statement);
		if($this->sql->errno)
			throw new SQLException($statement."\n"."CODE: ".$this->sql->error);

		// return the results based on certain conditions
		$r = null;
		if($this->isCounting && $results){ // if we are counting we only want the first row and first field back
			$row = $results->fetch_row();
			$r = $row[0];
		} elseif($results){ // otherwise we go through and create the respective model for them
			$records = array();
			while($row = $results->fetch_assoc()){
				$records[] = $rawArray ? $row : new $this->className($row);
			}
			
			$limit = isset($this->limits[0]) ? $this->limits[0] : false;
			$r = $limit === 1 ? current($records) : $records; // if we are expecting only 1 record back then simply return just the first record. Otherwise return all of them
		} else{ // invalid results 
			$r = false;
		}

		$results->close(); // properly close connection to release resources
		return $r;
	}


	// execute a raw query
	public function raw($query) {

		$query_items = array();	

		// query the database
		$raw_results = $this->sql->query($query);
		if($this->sql->errno)
			throw new SQLException($query."\n"."CODE: ".$this->sql->error);

		while($row = $raw_results->fetch_assoc()) { // fetch results and store them off
			$query_items[] = $row;
		}

		$raw_results->close();
		return $query_items;

	}



}
?>