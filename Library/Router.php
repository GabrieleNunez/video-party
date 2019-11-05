<?php namespace Library;

use Library\Exceptions\UnknownException;
use Library\Exceptions\RouteException;

class Router
{
    private static $routes = array('GET' => array(), 'POST' => array(), 'DELETE' => array(), 'PUT' => array());
    private static $active_route = '';
    private static $active_parameters = array();
    private static $active_filters = array();
    private static $active_method = '';
    private static $active_request_uri = '';
    private static $active_path_components = array();

    // load in the routes
    public static function LoadRoutes()
    {
        require_once Application::file('routes');
    }

    // run the router
    public static function Run($autoload_routes = true)
    {
        // set the  working method and request
        self::$active_method = $_SERVER['REQUEST_METHOD'];
        self::$active_request_uri = $_SERVER['REQUEST_URI'];

        $path = strtolower(self::$active_request_uri); // set our path to our request
        $path = explode('?', $path)[0]; // check for a query string. At zero index
        $path = strlen($path) > 1 ? rtrim($path, '/') : $path; //strip the slash at the end of the string (if its there). rtrim will take care of this automatically
        $parts = explode('/', $path);
        self::$active_path_components = $parts;

        if ($autoload_routes) {
            self::LoadRoutes();
        }

        self::traverseRoutes();
    }

    // Route to location
    public static function route($method, $url, $callback, $filters = array())
    {
        $method = strtoupper($method);

        $route = array();
        $parts = explode('/', $url);
        $level = self::$routes[$method]; // start off our level at our base trunk. Because we are using references, there is no need for me to copy back over results

        reset($parts);
        while (($part = current($parts)) !== false) {
            $identifier = '';
            $is_wildcard = substr($part, 0, 1) == '@';
            $identifier = $is_wildcard ? '@' : strtolower($part); // conver part to lowercase additionally just in case

            if (!isset($level[$identifier])) {
                // define the level
                $level[$identifier] = array();
            }

            $level = &$level[$identifier]; // descend into the new level
            $level['name'] = $is_wildcard ? substr($part, 1) : 'static';
            next($parts);
        }

        $level['filters'] = $filters;
        $level['*'] = $callback;
    }

    public static function get($url, $callback, $filters = array())
    {
        self::route('GET', $url, $callback, $filters);
    }

    public static function post($url, $callback, $filters = array())
    {
        self::route('POST', $url, $callback, $filters);
    }

    public static function batch($method, $routes, $filters = array())
    {
        $method = strtoupper($method);
        $total_routes = count($routes);
        for ($i = 0; $i < $total_routes; $i++) {
            self::route($method, current($routes[$i]), end($routes[$i]), $filters);
        }
    }

    public static function redirect($route, $target, $method = 'GET')
    {
        self::route($method, $route, function () use ($target) {
            header('Location: ' . $target);
            exit();
        });
    }

    // Process the current request
    private static function traverseRoutes()
    {
        $parameters = array(); // Start traversal process. These are the parameters that we will pass
        $level = self::$routes[self::$active_method]; // same as when we are registering. Grab a reference to our trunk

        // traverse the tree and determine if we have a valid route
        reset(self::$active_path_components);
        while (($part = current(self::$active_path_components)) !== false) {
            if (isset($level[$part])) {
                // found, now we move down one more level
                $level = &$level[$part];
            } elseif (isset($level['@'])) {
                // didn't find a defined, but we have a wild card. Go down this level
                $level = &$level['@'];
                $parameters[$level['name']] = urldecode($part);
            } else {
                throw new RouteException(self::$active_request_uri . ' route not found');
            }

            next(self::$active_path_components);
        }
        reset(self::$active_path_components);

        // we have reached the final level. Extract the required information and dig in
        $callback = $level['*'];
        self::$active_filters = $level['filters'];
        self::$active_parameters = $parameters;
        $can_execute = true;

        // before we even call the actual route run these filters s
        reset(self::$active_filters);
        while (($filter = current(self::$active_filters)) !== false && $can_execute) {
            if (is_callable($filter)) {
                $can_execute = $filter();
            } else {
                $filter_parts = explode('@', $filter);
                $filter_class = 'App\\Filters\\' . $filter_parts[0];
                $filter_function = $filter_parts[1];
                $filter_obj = new $filter_class();
                $can_execute = $filter_obj->{$filter_function}();
            }
            next(self::$active_filters);
        }
        reset(self::$active_filters);

        if ($can_execute) {
            // we can actually execute this piece of code. Successfully passed all filters
            if (is_callable($callback)) {
                $callback($parameters);
            } else {
                $callbackParts = explode('@', $callback);
                $class = 'App\\Controllers\\' . $callbackParts[0];
                $function = $callbackParts[1];
                $controller = new $class();
                return $controller->{$function}($parameters);
            }
        } else {
            // set up 403 Forbidden message
            header('HTTP/1.0 403 Forbidden', true, 403);
            exit();
        }
    }
}

?>
