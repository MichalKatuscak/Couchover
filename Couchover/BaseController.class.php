<?php
 
namespace Couchover;
 
// {{{ BaseController
 
/**
 * BaseController.class.php
 *
 * Base controller
 *
 * @author     Michal Katuščák <michal@katuscak.cz>
 * @license    Creative Commons 3.0 http://creativecommons.org/licenses/by/3.0/
 */
abstract class BaseController extends Object 
{
    // {{{ properties
 
    /**
     * Array with language
     *
     * @var array
     */
    public $lang;
 
    /**
     * Model of application
     *
     * @var object
     */
    public $model;
    
    /**
     * Parameters from URL
     *
     * @var array
     */
    public $parameters;
    
    /**
     * Template object
     *
     * @var object
     */
    public $template;
 
    // }}}
}
 
// }}}