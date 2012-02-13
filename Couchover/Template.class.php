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
     * Set URL of template
     *
     * @param string $view HTML
     */
    public function __construct ($view_url) {
        $this->view_url = $view_url;
    }
 
    // }}}

    // {{{ escape
 
    /**
     * Escape - chars: < > " ' &
     * 
     * @param string $string String to escape
     */
    public function escape ($string) {
        if (is_string($string)) {
            $chars = Array('&','<','>','"','\'');
            $replace = Array('&amp;','&lt;','&gt;','&quot;','&#39;');
            return str_replace($chars, $replace, $string);
        }
        return $string;
    }
 
    // }}}

    // {{{ unescape
 
    /**
     * Unescape - chars: < > " ' &
     * 
     * @param string $string String to unescape
     */
    public function unescape ($string) {
        if (is_string($string)) {
            $chars = Array('&amp;','&lt;','&gt;','&quot;','&#39;');
            $replace = Array('&','<','>','"','\'');
            return str_replace($chars, $replace, $string);
        }
        return $string;
    }
 
    // }}}

    // {{{ render
 
    /**
     * Render template
     */
    public function render () {
        Debugger::test('isArray', $this->vars, 'Template->render(): Template vars is Array?');
        
        foreach ($this->vars as $name=>$value) {
                // Escape chars: & < > " '
            $$name = $this->escape($value);
        }
        
        if (file_exists($this->view_url)) {
            include_once ($this->view_url);
        }
    }
 
    // }}}
}
 
// }}}