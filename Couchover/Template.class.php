<?php
 
namespace Couchover;
 
// {{{ Template
 
/**
 * Template.class.php
 *
 * Template
 *
 * @author     Michal Katuščák <michal@katuscak.cz>
 * @license    Creative Commons 3.0 http://creativecommons.org/licenses/by/3.0/
 */
final class Template
{
    // {{{ properties
 
    /**
     * Template URL
     *
     * @var string 
     */
    private $view_url;
 
    /**
     * Template vars
     *
     * @var array 
     */
    public $vars = Array();
 
    // }}}

    // {{{ __contruct
 
    /**
     * Working with database
     *
     * @param string $view HTML
     */
    public function __construct ($view_url) {
        $this->view_url = $view_url;
    }
 
    // }}}

    // {{{ __destruct
 
    /**
     * Render template
     */
    public function render () {
        Debugger::test('isArray', $this->vars, 'Template->render(): Template vars is Array?');
        
        foreach ($this->vars as $name=>$value) {
            $$name = $value;
        }
        
        if (file_exists($this->view_url)) {
            include_once ($this->view_url);
        }
    }
 
    // }}}
}
 
// }}}