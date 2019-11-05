<?php namespace Library\Bronco;

// Bronco Language Definition
class BroncoLanguage
{
    // reserved elements in the Bronco Template Language
    private static $reserved_elements = array(
        'bronco' => 'Explicity tells the Bronco Engine to process all logic in this block',
        'template' => 'Loads in an external template file for processing',
        'loop' => 'Process all html and bronco content in an iterative fashion',
        'evaluate' => 'Evaluate an expression and render as appropriate'
    );

    // reserved attributes in the Bronco Template Language
    private static $reserved_attributes = array(
        'bronco' => 'Explicity tells the Bronco engine to collect this element for processing',
        'evaluate' => 'Provide an expression to evaluate',
        'evaluate-true' => 'Execute the inline defined expression when it evaluates true',
        'evaluate-false' => 'Execute the inline defined expression when it evaluates false'
    );

    // reserved markers that indicate what kind of content we are dealing with
    private static $reserved_markers = array(
        '$' => 'variable',
        '@' => 'attribute'
    );

    // gets the reserved elements for Bronco
    public static function getElements()
    {
        return self::$reserved_elements;
    }

    // gets the reserved attributes for Bronco
    public static function getAttributes()
    {
        return self::$reserved_attributes;
    }

    // get the reserved markers
    public static function getMarkers()
    {
        return self::$reserved_markers();
    }
}
?>
