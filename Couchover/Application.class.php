<?php
 
namespace Couchover;
 
// {{{ Application
 
/**
 * Application.class.php
 *
 * Class to control the whole application.
 *
 * @author     Michal Katuščák <michal@katuscak.cz>
 * @license    Creative Commons 3.0 http://creativecommons.org/licenses/by/3.0/
 */
final class Application extends Object
{
    // {{{ properties

    /**
     * Application parameters
     *
     * @var array
     */
    private $parameters = Array();

    /**
     * Default language
     *
     * @var string
     */
    private $language_default = '';
 
    // }}}

    // {{{ configuration()
 
    /**
     * Configuring application and modules
     *
     * @param array $config Application settings
     */
    public function configuration ($config) {
        if (is_array($config)) {
            Debugger::configuration($config['debugger']);
            
            $this->language_default = $config['language-default'];
        }
    } 
 
    // }}}

    // {{{ router()
 
    /**
     * URL route
     *
     * @param object $router Current router
     */
    public function route ($router) {
        Debugger::test('isArray', $router->parameters, 'Application->route(): Parameters from router are in array');
        $this->parameters = $router->parameters;
    } 
 
    // }}}

    // {{{ run()
 
    /**
     * Run application
     */
    public function run () {
            // Start timer
        Debugger::timerStart();
        
            // Is controller and action selected?
        if (empty($this->parameters['controller']) || empty($this->parameters['action'])) {
            Debugger::error('Not selected controller or action.', E_USER_ERROR);
        }
        
            // Set controller and action name and language
        $controller_name = $this->parameters['controller'];
        $action_name = $this->parameters['action'];
        $language = $this->parameters['language']?:$this->language_default;
        
            // Set controller, model, view and language URL
        $controller_url = __DIR__ . '/../Application/Controllers/' . $controller_name . 'Controller.class.php';
        $model_url = __DIR__ . '/../Application/Models/' . $controller_name . 'Model.class.php';
        $view_url = __DIR__ . '/../Application/Views/' . $controller_name . 'View.html';
        $language_url = __DIR__ . '/../Languages/' . $language . '.php';
        
            // Set classes and method name
        $controller_class = '\\' . $controller_name . 'Controller';
        $model_class = '\\' . $controller_name . 'Model';
        $action_method = $action_name . 'Action';
        $lang = true;
        
        if (!file_exists($controller_url)) Debugger::error('Controller not found.', E_USER_ERROR);
        if (!file_exists($model_url)) Debugger::error('Model not found.', E_USER_ERROR);
        if (!file_exists($view_url)) Debugger::error('View not found.', E_USER_ERROR);
        if (!file_exists($language_url)) Debugger::error('Language not found.', E_USER_ERROR);
        
                
            // Load files
        include_once ($controller_url);
        include_once ($model_url);
        $view = file_get_contents($view_url);
        include_once ($language_url);
        
        Debugger::test('isArray', $lang, 'Application->run(): Language "'.$language.'" from file is not array');
        
        if (!class_exists($controller_class)) Debugger::error('Controller class not found.', E_USER_ERROR);
        if (!class_exists($model_class)) Debugger::error('Model class not found.', E_USER_ERROR);
        if (!method_exists($controller_class, $action_method)) Debugger::error('Action method not found.', E_USER_ERROR);
        
            // Load apllication
        $controller = new $controller_class;
        $controller->model = new $model_class;
        $controller->parameters = $this->parameters;
        $controller->template = new Template($view);
        $controller->lang = $lang;
        
            // Run application
        $controller->$action_method();

            // Render application
        $controller->template->render();
        
            // If Debugger level == 2 -> show debug panel
        if ($view = Debugger::view()) {
            echo $view;
        }
    }
 
    // }}}
}
 
// }}}