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
     * Template - HTML
     *
     * @var view
     */
    private $view;
 
    // }}}

    // {{{ __contruct
 
    /**
     * Working with database
     *
     * @param string $view HTML
     */
    public function __construct ($view) {
        $this->view = $view;
    }
 
    // }}}

    // {{{ __destruct
 
    /**
     * Render template
     */
    public function render () {
        echo $this->view;
    }
 
    // }}}
}
 
// }}}