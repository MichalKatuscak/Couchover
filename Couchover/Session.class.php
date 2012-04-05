<?php
 
namespace Couchover;

session_start();
 
// {{{ Session
 
/**
 * Session.class.php
 *
 * Working with session variables
 *
 * @author     Michal Katuščák <michal@katuscak.cz>
 * @license    Creative Commons 3.0 http://creativecommons.org/licenses/by/3.0/
 */
final class Session 
{
    // {{{ properties

    /**
     * UID
     *
     * @var string
     */
    public static $UID = '';
    
    // }}}

    // {{{ set
    /**
     * Set session
     * 
     * @param string $name Session name
     * @param mixed $value Session value
     * @return bool
     */
    public static function set ($name, $value) {
        $session_name = 'Couchover' . '-' . self::$UID . '-' . $name;
        if ($_SESSION[$session_name] = $value) {
            return true;
        }
        return false;
    }
    
    // }}}

    // {{{ get
    /**
     * Get session
     * 
     * @param string $name Session name
     * @return mixed
     */
    public static function get ($name) {
        $session_name = 'Couchover' . '-' . self::$UID . '-' . $name;
        if (isset($_SESSION[$session_name])) {
            return $_SESSION[$session_name];
        }
        return false;
    }
    
    // }}}

    // {{{ delete
    /**
     * Delete session
     * 
     * @param string $name Session name
     * @return bool
     */
    public static function delete ($name) {
        $session_name = 'Couchover' . '-' . self::$UID . '-' . $name;
        if (isset($_SESSION[$session_name])) {
            unset($_SESSION[$session_name]);
            return true;
        }
        return false;
    }
    
    // }}}

}
 
// }}}