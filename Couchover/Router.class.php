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
 
    // }}}
    
    // {{{ __construct
 
    /**
     * Create route
     */
    public function __construct ($rule, $default) {
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
    
}
 
// }}}