<?php

/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://wiki.kumbiaphp.com/Licencia
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@kumbiaphp.com so we can send you a copy immediately.
 *
 * Resultado de una consulta con ActiveRecord
 *
 * @category   Kumbia
 * @package    DbPool
 * @copyright  Copyright (c) 2005-2010 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace ActiveRecord\DbPool;

use \Iterator;

class ResultSet implements Iterator
{
    /**
     * Resultado de la consulta
     *
     * @var resource
     */
    protected $_result;

    /**
     * Posición del cursor
     *
     * @var int
     */
    private $_position = 0;

    /**
     * Constructor
     *
     */
    public function __construct ($result)
    {
        $this->_result = $result;
    }

    /**
     * fetch Array
     *
     * @return Array
     */
    public function fetchArray ()
    {
        return $this->_result->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch Object
     *
     * @param string Class
     * @return array
     */
    public function fetchObject ($class = 'stdClass')
    {
        return $this->_result->fetchObject($class);
    }

    /**
     * Fetch All
     *
     * @param int Fetch
     * @return ResultSet
     */
    public function fetchAll($fetch=PDO::FETCH_OBJ)
    {
        return $this->_result->fetchAll($fetch);
    }

    /**
     * Cantidad de filas afectadas por la sentencia
     *
     * @return int
     */
    public function affectRows ()
    {
        return $this->_result->rowCount();
    }

    /**
     * Reset resultset pointer
     * (implementation required by 'rewind()' method in Iterator interface)
     */
    public function rewind ()
    {
        $this->_pointer = 0;
    }

    /**
     * get current row set in result set
     * (implementation required by 'current()' method in Iterator interface)
     */
    public function current ()
    {
        if (! $this->valid()) {
            throw new \PDOException('Unable to retrieve current row.');
        }
        return $this->fetchObject();
    }

    /**
     * Obtiene la posición actual del Puntero
     *
     */
    public function key ()
    {
        return $this->_pointer;
    }

    /**
     * Mueve el puntero a la siguiente posición
     *
     */
    public function next ()
    {
        ++ $this->_pointer;
    }

    /**
     * Determina si el puntero del ResultSet es valido
     *
     * @return boolean
     */
    public function valid ()
    {
        return $this->_pointer < $this->_result->rowCount();
    }
}