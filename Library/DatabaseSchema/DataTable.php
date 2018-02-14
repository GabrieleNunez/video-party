<?php namespace Library\DatabaseSchema;

use Library\Application;

/* DataTable schema */
class DataTable {

	private $lookup = 'id';
	private $tablename = 'foo';
	private $connection = 'default';
	private $fields = array();
	private $indices = array();
	private $default_field_options = array(
		'primary' => false,
		'auto_increment' => false,
		'null' => true,
		'length' => '255',
		'type' => 'VARCHAR',
		'unsigned' => false,
	);
	private $default_index_options = array(
		'type' => 'INDEX',
		'prefix' => 'index',
		'method' => 'BTREE',
		'column' => 'id',
	);

	// construct the database table
	public function __construct($tablename, $connection = 'default') {
		$this->tablename = $tablename;
		$this->connection = $conection;
	}

	// add a field into it
	// $name = column name
	// $optios = array of options to append 
	public function field($name, $options) {
		$this->fields[$name] = array_merge($this->default_field_options, $options);
	}

	// create a primary key
	public function primaryKey($name, $options) {

		$supplied_options = array('primary' => true, 
								 'auto_increment' => true,
								 'null' => false,
								 'type' => 'INT',
								 'length' => '11',
								 'unsigned' => true);

		$this->fields[$name] = array_merge($supplied_options, $options);
		$this->indices = $this->createIndex('PRIMARY', $name, 'index_'.$name);

	}

	// generate a unique key on the database table
	public function uniqueKey($column) {

		$supplied_options = array(
			'type' => 'UNIQUE INDEX',
			'method' => 'BTREE',
			'prefix' => 'unique_index'
		);

		$this->indices[$column] = array_merge($supplied_options, $options);
	}

	// create an index
	public function createIndex($type, $column, $options = array()) {

		$supplied_options = array(
			'type' => 'INDEX',
			'column' => $column,
			'method' => '', // let mysql determine what is best. If they want to override they can use the $options to do so
		);

		$this->indices[$column] = array_merge($supplied_options, $options);

	}

	// drop the current database
	public function drop() {

		$sql = Application::connection($this->connection);
		$q = sprintf('DROP TABLE IF EXISTS `%s`', $sql->escape_string($this->tablename));

		echo $q;
	}

	// craate database table
	public function create() {

		$sql = Application::connection($this->connection);
		$q = 'CREATE TABLE IF NOT EXISTS `'.$this->tablename.'` (';

		// process table fields
		foreach($this->fields as $column => $definition) {
            
			$def_string = sprintf('`%s` %s(%s) %s %s',
                                    $sql->escape_string($column),
                                    $sql->escape_string($definition['type']),
                                    $sql->escape_string($definition['length']),
                                    $sql->escape_string($definition['unsigned'] ? 'UNSIGNED' : ''),
                                    $sql->escape_string($definition['null'] ? 'NOT NULL' : 'NULL'));

			$q .= $def_string.',';
            
		}
        
		$q = rtrim($q,',');

		// now go
		foreach($this->indices as $column => $definition) {
			$method = strlen($definition['method']) ? ' USING '.$sql->escape_string($definition['method']) : '';
			switch(strtoupper($definition['type'])) {
				case 'PRIMARY':
					$q = sprintf('PRIMARY KEY (`%s`)%s',  $sql->escape_string($column),$method);
					break;
				default:
					$q = sprintf('%s `%s` (`%s`)%s',
                                $sql->escape_string($definition['type']),
                                $sql->escape_string($definition['prefix'].'_'.$column),
                                $sql->escape_string($column),
                                $method);
					break;
			}
		}

		$q = rtrim($q,',');
		$q .= ') ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_unicode_ci;';

		echo $q;
		
	}
}

?>