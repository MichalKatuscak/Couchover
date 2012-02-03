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
        echo $this->lang['lorem'];
    }
 
    // }}}
    // 
    // {{{ secondAction
 
    /**
     * Second action
     */
    public function secondAction () {
        echo 'ID: '.htmlspecialchars($this->parameters['others'][0]);
    }
 
    // }}}
}
 
// }}}