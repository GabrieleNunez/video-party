<?php namespace Library;

use Library\Exceptions\SQLException;

class Model
{
    public $expected = array(); // help's define what is expected from the table, this a more explicit approach for defining, this is not required
    public $table = '';
    public $connection = 'default';

    protected $lookupAttribute = 'id';
    protected $attributes = array();

    private $populatedState = false;
    private $sql = false;

    public function __construct($attributes = array())
    {
        $this->sql = Database::connection($this->connection);
        $this->attributes = $attributes;
        if (count($this->attributes)) {
            $this->populatedState = true;
        }
    }

    // clean function for outputting to html. Called whenever toArray is invoked
    public static function htmlClean($attribute)
    {
        return htmlspecialchars($attribute, ENT_QUOTES);
    }

    // magic function for invoking sql builder
    public static function __callStatic($name, $args)
    {
        $modelName = get_called_class();
        $sqlBuilder = new SQLBuilder($modelName);
        switch (count($args)) {
            case 0:
                return $sqlBuilder->$name();
            case 1:
                return $sqlBuilder->$name($args[0]);
            case 2:
                return $sqlBuilder->$name($args[0], $args[1]);
            case 3:
                return $sqlBuilder->$name($args[0], $args[1], $args[2]);
            case 4:
                return $sqlBuilder->$name($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array($func, $args);
        }
    }

    // retrieve attributes
    public function __get($property)
    {
        return $this->attributes[$property];
    }

    // dynamically set attributes
    public function __set($property, $value)
    {
        $this->attributes[$property] = $value;
    }

    // manually assign attributes
    // NOTE: THIS WILL CLEAR all attributes currently assigned
    public function assign($attributes)
    {
        $this->attributes = $attributes;
    }

    // convert to an array
    // DEPRECATED please move to to_array
    public function toArray()
    {
        $attributes = array_map(array('Library\Model', 'htmlClean'), $this->attributes);
        return $attributes;
    }

    // this function for now only wrapps o
    public function to_array()
    {
        return $this->toArray();
    }

    // save the model
    public function save()
    {
        $lookup = '';
        if (isset($this->attributes[$this->lookupAttribute])) {
            $lookup = $this->lookupAttribute;
        } else {
            reset($this->attributes); // make sure we are at the beginning of this array
            $lookup = 'id';
        }

        if ($this->populatedState) {
            $columns = array();
            $values = array();
            foreach ($this->attributes as $column => $value) {
                $assignmentType = isset($this->expected[$column]) ? $this->expected[$column] : "'%s'";
                $columns[] = $column . ' = ' . $assignmentType;
                $values[$column] = $this->sql->escape_string($value);
            }
            $updateSet = implode(',', $columns);
            $updateSet = vsprintf($updateSet, $values);

            $lookupAssignment = isset($this->expected[$lookup]) ? $this->expected[$lookup] : "'%s'";
            $lookupString = sprintf($lookup . ' = ' . $lookupAssignment, $values[$lookup]);

            $statement = 'UPDATE ' . $this->table . ' SET ' . $updateSet . ' WHERE ' . $lookupString;
            $results = $this->sql->query($statement);
            if ($this->sql->errno) {
                throw new SQLException($statement . "\n" . $this->sql->error);
            }

            return $results ? true : false;
        } else {
            // not populated lets insert!

            $columns = array();
            $values = array();

            foreach ($this->attributes as $column => $value) {
                $insert_fmt = $value === null ? '%s' : "'%s'";
                $assignmentType = isset($this->expected[$column]) ? $this->expected[$column] : $insert_fmt;
                $columns[] = $column . ' = ' . $assignmentType;
                $values[$column] = $value === null ? 'NULL' : $this->sql->escape_string($value);
            }

            $insertSet = implode(',', $columns);
            $insertSet = vsprintf($insertSet, $values);

            $statement = 'INSERT INTO ' . $this->table . ' SET ' . $insertSet;
            $results = $this->sql->query($statement);
            if ($this->sql->errno) {
                throw new SQLException($statement . "\n" . $this->sql->error);
            }

            if ($results) {
                if ($lookup == 'id') {
                    // if the lookup is indeed an id field then store our insert id into it
                    $this->attributes['id'] = $this->sql->insert_id;
                }

                return true;
            } else {
                return false;
            }
        }
    }

    // provides a way to delete
    public function delete($field, $value)
    {
        if ($this->populatedState) {
            $query =
                'DELETE FROM ' . $this->table . ' WHERE ' . $field . ' = "' . $this->sql->escape_string($value) . '" ';
            $this->sql->query($query);
            if ($this->sql->errno) {
                throw new SQLException($query . "\n" . $this->sql->error);
            }
        }
    }

    public static function insertBulk($items)
    {
        $className = get_called_class();
        $baseModel = new $className(); // empty model

        if ($items) {
            // make sure we have actual elements to work with

            // establish a database connection using the connection string from our base model
            $sql = Database::connection($baseModel->connection);

            reset($items); // make sure we are pointing to our first element in our associative aray
            $key = key($items); // get current key in an associative array
            $columns = implode(',', array_keys($items[$key]));
            $statement = 'INSERT INTO ' . $baseModel->table . ' (' . $columns . ') VALUES';
            $records = array();

            foreach ($items as $item) {
                $string = '';
                $string .= '(';
                $values = array_values($item);
                $totalValues = count($values);
                for ($i = 0; $i < $totalValues; $i++) {
                    $values[$i] = $sql->escape_string($values[$i]);
                }
                $values = implode(',', $values);
                $string .= $values . ')';
                $records[] = $string;
            }

            $records = implode(',', $records);
            $statement .= $records;

            $results = $sql->query($statement);
            if ($sql->errno) {
                echo $sql->error;
            }
        } else {
            return true;
        }
    }
}
?>
