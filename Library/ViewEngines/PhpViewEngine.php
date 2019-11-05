<?php namespace Library\ViewEngines;

use Library\View;
use Library\Application;
use Library\ViewEngines\ViewEngine;

// A simple and straight raw PHP View
class PhpViewEngine extends ViewEngine
{
    private static $viewspath = '';
    private $fullpath = '';
    private $variables = array();

    // Instantiate a view and store the viewspath
    public function __construct($viewname)
    {
        if (!self::$viewspath) {
            self::$viewspath = realpath(Application::folder('views'));
        }

        $this->fullpath = self::$viewspath . $viewname;
    }

    // Instantiate a View statically
    public static function make($viewname)
    {
        return new PhpViewEngine($viewname);
    }

    // Go straight to rendering
    public function render()
    {
        ob_start();
        foreach ($this->variables as $variable => $value) {
            ${$variable} = $value;
        } // Use interpolation on the php side to dynamically create our variables
        require $this->fullpath;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    // Add a variable onto our stack to use within our view
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            // If it is an array, lets merge our results with our variables table
            $this->variables = array_merge($this->variables, $key);
        }
        // Not array lets set the key value pair outselves
        else {
            $this->variables[$key] = $value;
        }
        return $this;
    }

    public function link($templates = array())
    {
        // TODO implement link
        return $this;
    }

    // Empty our stored vars
    public function clear()
    {
        $this->variables = array();
    }

    // Render our view whenever this is called
    public function __toString()
    {
        return $this->render();
    }
}
?>
