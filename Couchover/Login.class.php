<?php
 
namespace Couchover;
 
// {{{ Login
 
/**
 * Login.class.php
 *
 * Class to login
 *
 * @author     Michal Katuščák <michal@katuscak.cz>
 * @license    Creative Commons 3.0 http://creativecommons.org/licenses/by/3.0/
 */
final class Login 
{

    // {{{ isLogged
    /**
     * Is the user logged in?
     * 
     * @param string $name Auth name
     * @return bool
     */
    public static function isLogged ($name = 'Default') {
        if (isset($_SESSION['couchover-' . $name . '-login']) && is_array($_SESSION['couchover-' . $name . '-login'])) {
            return true;
        }
        return false;
    }
    
    // }}}

    // {{{ set
    /**
     * Set login
     * 
     * @param string $name Auth name
     * @param array $data Data for save to SESSION
     * @return bool
     */
    public static function set ($name = 'Default', $data) {
        if (is_array($data)) {
            $_SESSION['couchover-' . $name . '-login'] = $data;
            return true;
        }
        return false;
    }
    
    // }}}

    // {{{ logout
    /**
     * Logout
     * 
     * @param string $name Timer name (for more measurements)
     * @return bool
     */
    public static function logout ($name = 'Default') {
         unset($_SESSION['couchover-' . $name . '-login']);
    }
    
    // }}}

}
 
// }}}