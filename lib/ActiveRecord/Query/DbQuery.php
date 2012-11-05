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
 * Clase para consultas SQL
 *
 * @category   Kumbia
 * @package    ActiveRecord
 * @subpackage Query
 * @copyright  Copyright (c) 2005-2009 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace ActiveRecord\Query;

class DbQuery
{

    /**
     * Partes de la consulta sql
     *
     * @var array
     */
    protected $sql = array();

    /**
     * Clausula DISTINCT
     *
     * @param boolean $distinct
     * @return DbQuery
     */
    public function distinct($distinct)
    {
        $this->sql['distinct'] = $distinct;
        return $this;
    }

    /**
     * Clausula WHERE con AND
     *
     * @param string $conditions condiciones AND
     * @return DbQuery
     */
    public function where($conditions)
    {
        $this->sql['where'][] = $this->_where($conditions);
        return $this;
    }

    /**
     * Clausula WHERE con OR
     *
     * @param string $conditions condiciones OR
     * @return DbQuery
     */
    public function whereOr($conditions)
    {
        $this->sql['where'][] = $this->_where($conditions, FALSE);
        return $this;
    }

    /**
     * Método interno para crear la Clusula WHERE
     *
     * @param string $conditions
     * @param bool   $type TRUE = AND; FALSE = OR
     * @return string clausula
     */
    protected function _where($conditions, $type = TRUE)
    {
        $cond = NULL;
        if (isset($this->sql['where'])) {
            if ($type === TRUE) {
                $cond = ' AND ';
            } else {
                $cond = ' OR ';
            }
        }
        return $cond . "($conditions)";
    }

    /**
     * Parámetros que seran enlazados a la setencia SQL
     *
     * @param array $bind
     * @return DbQuery
     */
    public function bind($bind)
    {
        foreach ($bind as $k => $v) {
            $this->sql['bind'][":$k"] = $v;
        }
        return $this;
    }

    /**
     * Parámetro que sera enlazado a la setencia SQL
     *
     * @param string $bind
     * @param string $value
     * @return DbQuery
     */
    public function bindValue($bind, $value)
    {
        $this->sql['bind'][":$bind"] = $value;
        return $this;
    }

    /**
     * Retorna los elementos para ser enlazados
     *
     * @return array
     */
    public function getBind()
    {
        if (isset($this->sql['bind'])) {
            return $this->sql['bind'];
        }
        return NULL;
    }

    /**
     * Clausula INNER JOIN
     *
     * @param string $table nombre de tabla
     * @param string $conditions condiciones
     * @return DbQuery
     */
    public function join($table, $conditions)
    {
        $this->sql['join'][] = array('table' => $table, 'conditions' => $conditions);
        return $this;
    }

    /**
     * Clausula LEFT OUTER JOIN
     *
     * @param string $table nombre de tabla
     * @param string $conditions condiciones
     * @return DbQuery
     */
    public function leftJoin($table, $conditions)
    {
        $this->sql['leftJoin'][] = array('table' => $table, 'conditions' => $conditions);
        return $this;
    }

    /**
     * Clausula RIGHT OUTER JOIN
     *
     * @param string $table nombre de tabla
     * @param string $conditions condiciones
     * @return DbQuery
     */
    public function rightJoin($table, $conditions)
    {
        $this->sql['rightJoin'][] = array('table' => $table, 'conditions' => $conditions);
        return $this;
    }

    /**
     * Clausula FULL JOIN
     *
     * @param string $table nombre de tabla
     * @param string $conditions condiciones
     * @return DbQuery
     */
    public function fullJoin($table, $conditions)
    {
        $this->sql['fullJoin'][] = array('table' => $table, 'conditions' => $conditions);
        return $this;
    }

    /**
     * Columnas de la consulta
     *
     * @param string $table nombre de tabla
     * @return DbQuery
     */
    public function table($table)
    {
        $this->sql['table'] = $table;
        return $this;
    }

    /**
     * Columnas de la consulta
     *
     * @param string $schema schema donde se ubica la tabla
     * @return DbQuery
     */
    public function schema($schema)
    {
        $this->sql['schema'] = $schema;
        return $this;
    }

    /**
     * Clausula SELECT
     *
     * @param string $criteria criterio de ordenamiento
     * @return DbQuery
     */
    public function order($criteria)
    {
        $this->sql['order'] = $criteria;
        return $this;
    }

    /**
     * Clausula GROUP
     *
     * @param string $columns columnas
     * @return DbQuery
     */
    public function group($columns)
    {
        $this->sql['group'] = $columns;
        return $this;
    }

    /**
     * Clausula HAVING
     *
     * @param string $conditions condiciones
     * @return DbQuery
     */
    public function having($conditions)
    {
        $this->sql['having'] = $conditions;
        return $this;
    }

    /**
     * Clausula LIMIT
     *
     * @param int $limit
     * @return DbQuery
     */
    public function limit($limit)
    {
        $this->sql['limit'] = $limit;
        return $this;
    }

    /**
     * Clausula OFFSET
     *
     * @param int $offset
     * @return DbQuery
     */
    public function offset($offset)
    {
        $this->sql['offset'] = $offset;
        return $this;
    }

    /**
     * Construye la consulta SELECT
     *
     * @param string $columns columnas
     * @return DbQuery
     */
    public function select($columns = NULL)
    {
        $this->sql['command'] = 'select';

        if ($columns) {
            $this->columns($columns);
        }

        return $this;
    }

    /**
     * Columnas a utilizar en el Query
     *
     * @param string $columns columnas
     * @return DbQuery
     */
    public function columns($columns)
    {
        $this->sql['columns'] = $columns;
        return $this;
    }

    /**
     * Construye la consulta DELETE
     *
     * @return DbQuery
     */
    public function delete()
    {
        $this->sql['command'] = 'delete';
        return $this;
    }

    /**
     * Construye la consulta UPDATE
     *
     * @param array $data claves/valores
     * @return DbQuery
     */
    public function update($data)
    {
        $this->bind($data);
        $this->sql['data'] = $data;
        $this->sql['command'] = 'update';
        return $this;
    }

    /**
     * Construye la consulta UPDATE
     *
     * @param string | array $data columnas, o array de claves/valores
     * @return DbQuery
     */
    public function insert($data)
    {
        $this->bind($data);
        $this->sql['data'] = $data;
        $this->sql['command'] = 'insert';
        return $this;
    }

    /**
     * Obtiene el array base con las partes de la consulta SQL
     *
     * @return array
     */
    public function getSqlArray()
    {
        return $this->sql;
    }

}
