<?php namespace Library;

use Library\Exceptions\BadRequestException;

class Request
{
    public $data;
    public $desired;
    public $missing;
    public $errors;

    // constructs a request object based on a desired method
    // $method either GET, POST or FILE
    // $desired a optional array of fields that you care about
    public function __construct($method, $desired = array(), $required = false)
    {
        // setup
        $singleMethod = !is_array($method);
        $hasFilter = count($desired) ? true : false;
        $filter = array_fill_keys($desired, null);
        $methods = $singleMethod ? array($method) : $method;

        // prepare our data and oop through our methods that we want
        $this->errors = array();
        $this->desired = $desired;
        $this->data = array();
        foreach ($methods as $meth) {
            switch ($meth) {
                case 'GET': // GET request
                    $this->data += $hasFilter ? array_intersect_key($_GET, $filter) : $_GET;
                    break;
                case 'POST': // POST request
                    $this->data += $hasFilter ? array_intersect_key($_POST, $filter) : $_POST;
                    break;
                case 'FILE': // FILE request
                    $this->data += $hasFilter ? array_intersect_key($_FILES, $filter) : $_FILES;
                    break;
                default:
                    // unknown request
                    throw new BadRequestException('Unsupported request method');
                    break;
            }
        }

        // if we passed in our desired array that means we have expectations for our request
        // check and make sure we have them otherwise throw exception
        $missing = array_diff_key($this->data, $filter);
        if ($required && count($missing)) {
            $missingString = implode(',', $missing);
            throw new BadRequestException('Missing the following fields - ' . $missingString);
        }

        $this->missing = array_keys($missing);
    }

    // get any missing fields
    public function get_missing()
    {
        return $this->missing;
    }

    // retrieve a field value from this request
    public function get($field)
    {
        return isset($this->data[$field]) ? $this->data[$field] : null;
    }

    // get the timestamp of the current request
    public function get_timestamp()
    {
        return $_SERVER['REQUEST_TIME'];
    }

    // determine if the request was/is an ajax request
    public static function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
?>
