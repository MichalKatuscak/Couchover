<?php
 
namespace Couchover;
 
// {{{ Router
 
/**
 * Router.class.php
 *
 * Routing URL
 *
 * @author     Michal Katuščák <michal@katuscak.cz>
 * @license    Creative Commons 3.0 http://creativecommons.org/licenses/by/3.0/
 */
final class Router
{
    // {{{ properties

    /**
     * Application parameters
     *
     * @var array
     */
    public $parameters = Array();

    /**
     * Route rule
     *
     * @var string
     */
    private $rule = '';

    /**
     * Default route
     *
     * @var string
     */
    private $default = '';
 
    // }}}
    
    // {{{ __construct
 
    /**
     * Create route
     */
    public function __construct ($rule, $default) {
        $this->rule = $rule;
        $this->default = $default;
        
            // Is it relative URL route?
        if (array($rule[0],$rule[1],$default[0],$default[1]) == array('/','/','/','/')) {
                // Transform "//relative_url" -> "relative_url"
            $rule = substr($rule, 2);
            $default = substr($default, 2);
            $route_type = 'relative';
        } else {
            $route_type = 'absolute';
        }
        
        if ($route_type == 'relative') {
            $url = isset($_GET['url']) ? $_GET['url'] : $default;
            
                // Get parameters from URL
            $parameters = $this->parseUrlToParams($url, $rule, $default);
            
                // Set parameters for Application
            $this->parameters = $parameters;
            
        } else {
            Debugger::error('Error in the routing rule. It does not support absolute route.', E_USER_ERROR);
        }
    }
 
    // }}}
    
    // {{{ parseUrlToParams
 
    /**
     * Parse URL to parameters
     * 
     * @param string $url
     * @param string $rule
     * 
     * @return array $parameters
     */
    private function parseUrlToParams ($url, $rule, $default) {  
            $parameters = Array();
            $parameter_names = explode('/', $rule);
            $parameter_values = explode('/', $url);
            $parameter_value_default = explode('/', $default);
            foreach ($parameter_names as $key=>$name) {
                if ($name != '' && $name[0] == ':' && (isset($parameter_values[$key]) || isset($parameter_value_default[$key]))) {
                    $name[0] = '';
                    $value = (isset($parameter_values[$key]) && $parameter_values[$key] != '')?$parameter_values[$key]:$parameter_value_default[$key];
                    $parameters[trim($name)] = $value;
                    unset($parameter_values[$key]);
                } 
            }
            $parameters['others'] = array_values(array_filter($parameter_values, 'strlen'));
            
            return $parameters;
    }
 
    // }}}
    
    // {{{ createLink
 
    /**
     * Parameters to URL
     * 
     * @param array $parameters URL parameters
     * 
     * @return string $url
     */
    public function createLink ($parameters) {  
            $url = './';
            $parameter_names = explode('/', $this->rule);
            $parameter_values = $parameters;
            $parameter_value_default = explode('/', $this->default);
            foreach ($parameter_names as $key=>$name) {
                $full_name = str_replace(':','',$name);
                if ($name != '' && $name[0] == ':' && (isset($parameter_values[$full_name]) || isset($parameter_value_default[$key]))) {
                    $url .= (isset($parameter_values[$full_name]) && $parameter_values[$full_name] != '')?$parameter_values[$full_name]:$parameter_value_default[$key];
                    $url .= '/';
                } 
            }
            
            if (isset($parameters['others'])) {
                if (is_array($parameters['others'])) {
                    foreach ($parameters['others'] as $other) {
                        $url .= $other;
                        $url .= '/';
                    }
                } else {
                    $url .= $parameters['others'];
                    $url .= '/';
                }
            }
            
            return $url;
    }
 
    // }}}
    
}
 
// }}}