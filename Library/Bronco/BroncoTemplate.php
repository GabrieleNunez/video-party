<?php
namespace Library\Bronco;

use Library\Bronco\BroncoEngine;
use Library\Bronco\BroncoException;
use Library\Bronco\BroncoLanguage;

use DOMDocument;
use DOMXPath;
use Traversable;

// Class that represents a template in bronco. Handdles processing logic
class BroncoTemplate {
    
    private $template_path = '';
    private $document = null;
    private $xpath = null;
    private $content = '';
    private $html_flags = 0;
    private static $options = array();
    
    // constructs a bronco template and passes in the name
    public function __construct($template) {
        
        $this->template_path = self::$options['view_directory'].$template;  
        if(!file_exists($this->template_path))
            throw new BroncoException('This template does not exist');
 
        $this->html_flags = LIBXML_ERR_NONE | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOXMLDECL | LIBXML_NOWARNING | LIBXML_PARSEHUGE;
    }
    
	// adjust the option value or retrieve if only supply the name of the option
	public static function option($option_name, $value = null) {
		if($value === null) {
			return isset(self::$options[$option_name]) ? self::$options[$option_name] : null;
        } else {
			self::$options[$option_name] = $value;
            
        }
    }
    
    // set a mass amount of options at once
    public static function setOptions($options) {
        self::$options = array_merge(self::$options,$options);
    }
    
    // process/ and create our template
    public function process(&$variables) {
        
        $content_hash = md5(filemtime($this->template_path).$this->template_path.json_encode($variables,JSON_FORCE_OBJECT));
		$cache_file = self::$options['view_cache'].DIRECTORY_SEPARATOR.$content_hash;
        
        if(BroncoCache::exist($cache_file)) { // we are cached we can go ahead and just simply 
			$this->content =  BroncoCache::read($cache_file);
		} else { // uncached, we need to process
            $this->content = file_get_contents($this->template_path);
            $this->parseChildren($variables);
            $this->processLoops($variables);
            $this->processConditions($variables);
            $this->processInline($variables);
            $this->content = $this->injectVariables($this->content, $variables);
            BroncoCache::write($this->content, $cache_file);
        }  
        
    }
    
    // gets the XPATH of the Document
    public function getXPath() {
        return $this->xpath;
    }
    
    // gets the Document
    public function getDocument() {
        return $this->document;
    }
    
    // content
    public function getContent() {
        return $this->content;
    }
    
    private function evaluate_expression($expression, &$variables) {
        
        $has_expression = strlen($expression) > 0;
       
        // valid operators that we support currently
        $valid_operators = array(
            '==',
            '===',
            '!=',
            '!==',
            '>',
            '<',
            '>=',
            '<=',
        );
        
        // build up the operator list
        $operator_search = '';
        foreach ($valid_operators as $operator)
            $operator_search .= preg_quote($operator) . '|';
        $operator_search = rtrim($operator_search, '|');
        
        // construct the regex and ifnd our matches
        $matches = array();
        $regex   = '/(.*?)(' . $operator_search . ')(.*)/s';
        $regex_match = preg_match($regex, $expression, $matches);
        
        $expression_results = null;
        $has_match = $regex_match ? strlen($matches[2]) > 0 : false;
        
        if ($has_match && $has_expression) { // treat as explicit expression ( left side, operator, right side)
            
            $left_side  = trim($matches[1]);
            $operator   = trim($matches[2]);
            $right_side = trim($matches[3]);
            
            
            if (!strlen($right_side))
                throw new BroncoException("Right hand expression is not present");
           
            $transformed_left = $this->parse_variable($left_side, $variables);
            $transformed_right = $this->parse_variable($right_side, $variables);

            $expression_results = false;
            switch ($operator) {
                case '==':
                    $expression_results = ($transformed_left == $transformed_right);
                    break;
                case '===':
                    $expression_results = ($transformed_left === $transformed_right);
                    break;
                case '!=':
                    $expression_results = ($transformed_left != $transformed_right);
                    break;
                case '!==':
                    $expression_results = ($transformed_left !== $transformed_right);
                    break;
                case '>':
                    $expression_results = ($transformed_left > $transformed_right);
                    break; 
                case '<':
                    $expression_results = ($transformed_left < $transformed_right);
                    break;
                case '>=':
                    $expression_results = ($transformed_left >= $transformed_right);
                    break;
                case '<=' :
                    $expression_results = ($transformed_left <= $transformed_right);
                    break;
                default:
                    $expression_results = false;
                    break;
            }


        } elseif (!$has_match && $has_expression) { // treat as implicit expression
           
           // determine if the first character is a ! which will make it a not true check otherwise assume we are going for truthy
           $is_falsy = strpos($expression, '!') === 0;
           $variable = ltrim(trim($expression),'!');
           $transformed_variable = $this->parse_variable($variable, $variables);
           $expression_results = ($is_falsy  ? (!$transformed_variable ? true : false) 
                                             : ($transformed_variable ? true : false));
                                                                              
        } else { // nothing there. Null expression
            $expression_results = false;
        }
        
        return $expression_results;
    }
       
    // handle processing a string that represents a variable into an equivalent form in php
    private function parse_variable($var_string, &$variables) {
        
        $variable = null;
        $reserved_nonvariables = array(
            'true',
            'false',
            'null'
        );
        
        if (is_numeric($var_string)) { // this is a numeric and we should cast to float
            $variable = (float)$var_string;
        } elseif (in_array(strtolower($var_string), $reserved_nonvariables)) { // this is a reserved keyword
            switch ($var_string) {
                case 'true':
                    $variable = true;
                    break;
                case 'false':
                    $variable = false;
                    break;
                case 'null':
                    $variable = null;
                    break;
                default:
                    $variable = null;
                    break;
            }
        } elseif( (strpos($var_string, '"') === 0 && strrpos($var_string,'"') === 0) || (strpos($var_string,"'") === 0 && strrpos($var_string, "'") === 0)) {
            $variable = trim($var_string,'"\'');
        } elseif( strpos($var_string, '$') === 0) {
            $variable = trim($var_string,'$');
        } else { // must be a real variable then
            
            $found = false;
            $variable  = $this->transform($variables, $var_string, $found);
            if(!$found)
                $variable = null;
                
        }

        return $variable;
    }
    
    // process conditions
    public function processConditions(&$variables) {
        
        $matches = array();
        preg_match_all('/<expression(.*?)\>(.*?)\<\/expression\>/s', $this->content, $matches);
        foreach ($matches[1] as $original => $keys) {
            $src_content = $matches[2][$original];
            $attributes = $this->extractHtmlAttr($keys);
            $result = $this->evaluate_expression($attributes['value'], $variables);
            
            $condition_content = '';
            $condition_attributes = array();
            if($result) { // look for a <true> block, if none is explicit then assume the entire block is provided only if true 
                $expression_matches = array();
                $regex_option = preg_match('/<true(.*?)\>(.*?)\<\/true\>/s', $src_content, $expression_matches);
                
                $condition_content = $regex_option && strlen($expression_matches[2]) ? $expression_matches[2] : $src_content;
                $condition_attributes = $regex_option && strlen($expression_matches[1]) ? trim($expression_matches[1]): array();
            } else { // look for a <false> element. If none is explicity set then go ahead and the entire block will be ignored
                $expression_matches = array();
                $regex_match = preg_match('/<false(.*?)\>(.*?)\<\/false\>/s', $src_content, $expression_matches);
                
                $condition_content = $regex_match && strlen($expression_matches[2]) ? $expression_matches[2] : '';
                $condition_attributes = $regex_match && strlen($expression_matches[1]) ? trim($expression_matches[1]): array();   
            }
      
            $this->content = str_replace($matches[0][$original], $condition_content, $this->content);
        } 
           
    }
    
    // process loop strings
    private function processLoops(&$variables) {
        
        $matches = array();
        preg_match_all('/\<loop(.*?)\>(.*?)\<\/loop\>/s', $this->content, $matches);
        
        if (isset($matches[2])) {
            foreach ($matches[1] as $original => $keys) {
                
                $src_content = $matches[2][$original];
                $attributes  = $this->extractHtmlAttr($keys);
                
                $loop_content = '';
                $array_source = $this->transform($variables, $attributes['src']);
                $var_key  = isset($attributes['key']) ? $attributes['key'] : '';
                $var_value = $attributes['value'];
               
                // determine if its possible to iterate through this source
                if(!is_array($array_source) && !($array_source instanceof Traversable))
                    throw new BroncoException('Invalid Source: '.$attributes['src']);
               
                foreach ($array_source as $key => $value) {
                    
                    $sub_content = $src_content;
                    if (strlen($var_key))
                        $sub_content = str_replace('$' . $var_key, $key, $sub_content);
                    
                    $sub_content = str_replace('$' . $var_value, '$' . $attributes['src'] . '.' . $key, $sub_content);
                    $loop_content .= $sub_content;
                    
                }
                
                $this->content = str_replace($matches[0][$original], $loop_content, $this->content);
                
            }
        }
        
    } 


    
    // parse children in the currently loaded content
    private function parseChildren(&$variables) {
        
        $this->document = new DOMDocument();
        libxml_use_internal_errors(true);
        $this->document->loadHTMLFile($this->template_path, $this->html_flags);
        libxml_clear_errors();
        libxml_use_internal_errors(false);
        
        $this->xpath = new DOMXPath($this->document);
        
        $processing_elements = $this->xpath->query('//template[@bronco]');
        $processed_children  = array();
        foreach ($processing_elements as $element) {
            
            $src = $element->getAttribute('src');
            if (!isset($processed_children[$src])) {
                $child_template = new BroncoTemplate($src);
                $child_template->process($variables);
                $processed_children[$src] = $child_template->getContent();
            }
            
        }
        
        $matches = array();
        preg_match_all("/(?:<template).*src\=['\"]([\/\a-zA-Z0-9\.\_]+?)['\"].*\/>|(?:<template).*src\=['\"]([\/\a-zA-Z0-9\.\_]+?)['\"].*\/?\>(?:.*<\/template\>)|(?:<template).*src\=['\"]([\/\a-zA-Z0-9\.\_]+?)['\"].*\/?\>(?:.*<\/template\>)/", $this->content, $matches);
        
        if (isset($matches[1])) {
            foreach ($matches[1] as $index => $src) {
                $target_src = strlen(trim($src)) ? trim($src) : $matches[2][$index];
                $this->content = str_replace($matches[0][$index], $processed_children[$target_src], $this->content);
            }
        }
        
    }
    
        // process inline variables
    private function processInline(&$variables) {

        $matches = array();
        preg_match_all('/<(.*?)(eval.*?)>/', $this->content, $matches);
        
        //\Library\Printout::write($matches);
        foreach($matches[2] as $original => $match) {
            $element = $matches[0][$original];
            $attributes = $this->extractHtmlAttr($element);

            $expression_evaluation = $this->evaluate_expression($attributes['evaluate'], $variables);
            if($expression_evaluation === true && isset($attributes['evaluate-true'])) {
                $true_action = $attributes['evaluate-true'];
                $sides = explode('=', $true_action);
                $attribute_to_set = current($sides);
                $attribute_value = end($sides);
            } elseif($expression_evaluation === false && isset($attributes['evaluate-false']))  {
                $false_action = $attributes['evaluate-false'];
                $sides = explode('=', $true_action);
                $attribute_to_set = current($sides);
                $attribute_value = end($sides);
            } else { // not applicable. Skip
                $attribute_to_set = null;
                $attribute_value = null;
            }


            // filter the element first and prepare to inject/adjust attributes
            $filtered_element = preg_replace('/evaluate(?:.*?)=[\'"](.*?)[\'"]/s', '', $element);
            if($attribute_to_set !== null && $attribute_value !== null) {
                if(strpos($filtered_element, $attribute_to_set) === false) { // attribute does not exist. Regex to make it exist
                    $filtered_element = substr_replace($filtered_element,' '.$attribute_to_set.'="'.$attribute_value.'">', strrpos($filtered_element,'>',0)); // insert into this position'
                } else { // attribute does exist. Need to regex to modify it

                }
            }

            $this->content = str_replace($element, $filtered_element, $this->content);
         //   \Library\Printout::write(array('post' => $this->content, 'pre' => $pre_content));
        }
    }
    

    // extract the html attributes from a set content
    private function extractHtmlAttr($content) {
        
        $attributes = array();
        $matches = array();
        preg_match_all('/([a-zA-Z0-9\_\-]+)\=[\'"](.*?)[\'"]/s', $content, $matches);
        if (isset($matches[1])) {
            foreach ($matches[1] as $index => $key) {
                $attributes[$key] = $matches[2][$index];
            }
        }
        return $attributes;
    }
    
    // inject variables
    private function injectVariables($content_data, $variables) {
        return preg_replace_callback('/\$([a-zA-Z\_][a-zA-Z0-9\.\_]+)/', function($matches) use ($variables) {
            $fin = $this->transform($variables, $matches[1]);
            return is_array($fin) ? json_encode($fin) : $fin;
        }, $content_data);
    }
    
    // take the key ( foo.bar ) and get it from our variables $variables['foo']['bar']
    private function transform(&$variables, $key, &$found = false) {
        
        $portions = explode('.', $key);
        
        $total_levels = count($portions);
        if ($total_levels == 1) { // there is only one possibility 
            $found = isset($variables[$portions[0]]);
            return $found ? $variables[$portions[0]] : '';
        } elseif ($total_levels > 1) {
            
            reset($portions); // reset internal pointer
            $variable = &$variables;
            $escape = false;
            while (($level = current($portions)) !== false && !$escape) { // cascade through our variables as needed
                if (isset($variable[$level]))
                    $variable =& $variable[$level];
                else
                    $variable = null;
                $escape = $variable === null;
                $found  = $variable !== null;
                
                next($portions);
            }
            
            return $escape ? '' : $variable;
        }
    }
}

?>