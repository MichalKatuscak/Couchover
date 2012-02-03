<?php
 
namespace Couchover;
 
// {{{ Object
 
/**
 * Object.class.php
 *
 * Base class of all.
 *
 * @author     Michal Katuščák <michal@katuscak.cz>
 * @license    Creative Commons 3.0 http://creativecommons.org/licenses/by/3.0/
 */
abstract class Object 
{
    // {{{ __set()
 
    /**
     * Avoiding setting uninitialized properties
     */
    public function __set ($name, $value) {
        throw new \Exception('Unable write to the property '.$name);
    }
 
    // }}}
}
 
// }}}

