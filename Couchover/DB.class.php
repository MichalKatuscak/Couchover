<?php

namespace Couchover;

// {{{ DB
 
/**
 * DB.class.php
 *
 * Class for working with database
 *
 * @author     Michal Katuščák <michal@katuscak.cz>
 * @license    Creative Commons 3.0 http://creativecommons.org/licenses/by/3.0/
 */
final class DB 
{        
    // {{{ properties

    /**
     * Database session
     *
     * @var object
     */
    private $connection;  

    /**
     * Type of database
     *
     * @var string
     */
    private $type = 'MySQLi'; 

    /**
     * SQL string
     *
     * @var string
     */
    private $query = '';
    
    // }}}

    // {{{ __construct()
 
    /**
     * Connect to the database and set default character set UTF-8
     *
     * @param string $type Type of database
     * @param string $server Server of database
     * @param string $username Username
     * @param string $password Password
     * @param string $database Name of database
     * @param int $port Port                         
     */
    public function __construct ($type = 'MySQLi', $server = 'localhost', $username = 'root', $password = '', $database = 'test', $port = 3306) {
        $this->type = $type;
        if ($this->type == 'MySQLi') {
            $this->connection = new \MySQLi($server, $username, $password, $database, $port);
            if ($this->connection->connect_errno) {
                Debugger::error('Failed to connect to MySQLi: (' . $this->connection->connect_errno . ') ' . $this->connection->connect_error, E_USER_ERROR);
            }
            $this->connection->query('SET NAMES utf8;');
        }
    } 
    
    // }}}

    // {{{ query()
 
    /**
     * Query execution
     *
     * @param string $query SQL query
     * @return object Result of query                            
     */
    public function query ($query) {
        if ($result = $this->connection->query($query)) {
            return $result;
        }
        Debugger::error('<span title="' . $query . '">Query failed</span>: (' . $this->connection->errno . ') ' . $this->connection->error, E_USER_ERROR);
        return 0;
    }
    
    // }}}

    // {{{ multiQuery()
 
    /**
     * MultiQuery execution
     *
     * @param string $query SQL query
     * @return object Result of query                            
     */
    public function multiQuery ($query) {
        if ($result = $this->connection->multi_query($query)) {
            return $result;
        }
        Debugger::error('<span title="' . $query . '">Query failed</span>: (' . $this->connection->errno . ') ' . $this->connection->error, E_USER_ERROR);
        return 0;
    }
    
    // }}}

    // {{{ fetchArray()
 
    /**
     * Fetch result as array
     *
     * @param object $result Result of query
     * @param int $type Type of result array (number, assoc)     
     * @return array Result of query                            
     */
    public function fetchArray ($result, $type = MYSQLI_ASSOC) {
        for ($res = array(); $tmp = $result->fetch_array($type);) $res[] = $tmp;
        return $res;
    }
    
    // }}}

    // {{{ select()
 
    /**
     * Start SELECT query
     *
     * @param string $what Select what
     * @return object This                            
     */
    public function select ($what) {
        $this->query = 'SELECT ' . addslashes($what);
        return $this;
    }
    
    // }}}

    // {{{ from()
 
    /**
     * Add FROM to Query
     *
     * @param string $table Table
     * @return object This                          
     */
    public function from ($table) {
        $this->query .= ' FROM ' . addslashes($table);
        return $this;
    }
    
    // }}}

    // {{{ where()
 
    /**
     * Add WHERE to Query
     *
     * @param mixed
     * @return object This                          
     */
    public function where () {
        $this->query .= ' WHERE ';    
        $args = func_get_args();
        
        if (isset($args[1])) {
            $template = $args[0];
            unset($args[0]);
            foreach ($args as $key=>$value) {
                if (is_string($value)) {
                    $args[$key] = '\'' . addslashes($value) . '\'';
                }
            }
            $this->query .= vsprintf($template, $args);
        } elseif (isset($args[0])) {
            $this->query .= $args[0];
        }
        return $this;
    }
    
    // }}}

    // {{{ order()
 
    /**
     * Add ORDER to Query
     *
     * @param string $by Column
     * @param strinf $desc DESC || ''     
     * @return object This                          
     */
    public function order ($by, $desc = '') {
        $this->query .= ' ORDER BY ' . addslashes($by) . ' ' . $desc;
        return $this;
    } 
    
    // }}}

    // {{{ limit()
 
    /**
     * Add LIMIT and OFFSET to Query
     *
     * @param int $limit Num of rows
     * @param int $offset Start row    
     * @return object Result of query                         
     */
    public function limit ($limit, $offset = 0) {
        $this->query .= ' LIMIT ' . (int) $limit;
        if ($offset != 0) {
            $this->query .= ' OFFSET ' . (int) $offset;
        }
        return $this->query($this->query);
    } 
    
    // }}}

    // {{{ update()
 
    /**
     * Start UPDATE query
     *
     * @param string $table Table
     * @param array $data Data to UPDATE
     * @return object This                          
     */
    public function update ($table, $data) {
        $this->query = 'UPDATE ' . addslashes($table) . ' SET ';
        $i = 0;
        foreach ($data as $key=>$value) {
            if ($i == 1) $this->query .= ',';
            $this->query .= $key . ' = \'' . addslashes($value) . '\'';
            $i = 1;
        }  
        return $this;
    }
    
    // }}}

    // {{{ insert()
 
    /**
     * Start INSERT query
     *
     * @param string $table Table
     * @param array $data Data to INSERT     
     * @return object Result of query                          
     */
    public function insert ($table, $data) {
        $this->query = 'INSERT INTO ' . addslashes($table) . ' (';
        $i = 0;
        $values = '';
        foreach ($data as $key=>$value) {
            if ($i == 1) {
                $this->query .= ',';
                $values .= ',';
            }
            $this->query .= addslashes($key);
            $values .= '\'' . addslashes($value) . '\'';
            $i = 1;
        }
        $this->query .= ') VALUES (' . $values . ')';
        return $this->query($this->query);
    }
    
    // }}}

    // {{{ delete()
 
    /**
     * Start DELETE query
     *
     * @param string $table Table    
     * @return object This                         
     */
    public function delete ($table) {
        $this->query = 'DELETE FROM ' . addslashes($table);
        return $this;
    }
    
    // }}}
}