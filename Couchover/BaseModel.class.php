<?php
 
namespace Couchover;
 
// {{{ BaseModel
 
/**
 * BaseModel.class.php
 *
 * Base model
 *
 * @author     Michal Katuščák <michal@katuscak.cz>
 * @license    Creative Commons 3.0 http://creativecommons.org/licenses/by/3.0/
 */
abstract class BaseModel extends Object
{
    // {{{ properties
 
    /**
     * Working with database
     *
     * @var object
     */
    public $db;
 
    // }}}
}
 
// }}}