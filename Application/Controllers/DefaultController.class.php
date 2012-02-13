<?php
 
use Couchover\BaseController;

// {{{ DefaultController
 
/**
 * DefaultController.class.php
 *
 * Default controller
 *
 * @author     Michal Katuščák <michal@katuscak.cz>
 * @license    Creative Commons 3.0 http://creativecommons.org/licenses/by/3.0/
 */
final class DefaultController extends BaseController
{     
    // {{{ defaultAction
 
    /**
     * Default action
     */
    public function defaultAction () {
        $tpl_vars = array();
        $tpl_vars['title'] = 'Titulek stránky';
        $tpl_vars['headline'] = '<i>Nadpis</i> stránky';
        $tpl_vars['style'] = 'color:blue;\' onclick=\'alert("XSS")';
        $this->template->vars = $tpl_vars;
    }
 
    // }}}
}
 
// }}}