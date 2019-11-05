<?php namespace Library\ViewEngines;

use Library\Application;
use Library\ViewEngines\ViewEngine;
use Library\Bronco\BroncoEngine;
use Library\Bronco\BroncoException;

class BroncoViewEngine extends ViewEngine
{
    private $variables = array();
    private $template = null;

    public function __construct($viewname)
    {
        if (!BroncoEngine::isStarted()) {
            BroncoEngine::start(array(
                'view_cache' => realpath(Application::setting('viewcache')),
                'view_directory' => realpath(Application::folder('views')),
                'cache' => Application::setting('bronco_cache')
            ));
        }

        $this->template = $viewname;
    }

    // construct the view statically
    public static function make($viewname)
    {
        return new BroncoViewEngine($viewname);
    }

    // include variables
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

    // link the following template(s) to check when utilizing cache
    public function link($template)
    {
        // TODO add linkeed in code
        return $this;
    }

    // clear stack variables
    public function clear()
    {
        $this->variables = array();
    }

    // render the view
    public function render()
    {
        return BroncoEngine::render($this->template, $this->variables);
    }
    // magic function to render to string
    public function __toString()
    {
        try {
            return $this->render();
        } catch (BroncoException $exception) {
            return $exception->getMessage();
        }
    }
}

?>
