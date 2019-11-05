<?php namespace Library\Bronco;

use Library\Bronco\BroncoException;
use Library\Bronco\BroncoTemplate;
use Library\Bronco\BroncoCache;

/* Simple and efficient view templating engine */
class BroncoEngine
{
    // start the engine
    private static $started = false;
    private static $default_options = array(
        'cache' => true,
        'view_cache' => '../tmp/views',
        'view_directory' => '../app/views'
    );

    private static $sorted_variables = array();

    // start the bronco engine
    public static function start($options)
    {
        if (!self::$started) {
            $merged_options = array_merge(self::$default_options, $options);
            BroncoTemplate::setOptions($merged_options);
            BroncoCache::enable($options['cache']);
            self::$started = true;
        }
    }

    // adjust the option value or retrieve if only supply the name of the option
    public static function option($option_name, $value = null)
    {
        if ($value === null) {
            return BroncoTemplate::option($option_name, $value);
        } else {
            BroncoTemplate::option($option_name, $value);
        }
    }

    // have we started bronco up yet ?
    public static function isStarted()
    {
        return self::$started;
    }

    protected static function deep_sort(&$array_data)
    {
        ksort($array_data);
        reset($array_data);
        while (($key = key($array_data)) !== null) {
            if (is_array($array_data[$key])) {
                self::deep_sort($array_data[$key]);
            }
            next($array_data);
        }
        reset($array_data);
    }

    // render the specified template
    public static function render($template, $variables = array())
    {
        if (!self::$sorted_variables) {
            // we have yet to encode our variables. Go ahead and do so as neccesarily
            self::$sorted_variables = $variables;
            self::deep_sort(self::$sorted_variables);
        }

        $template = new BroncoTemplate($template);
        $template->process(self::$sorted_variables);

        return $template->getContent();
    }
}
?>
