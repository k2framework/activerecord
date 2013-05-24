<?php

namespace ActiveRecord\Event;

use ActiveRecord\Model;
use ActiveRecord\PDOStatement;
use K2\EventDispatcher\Event as Base;

class Event extends Base
{

    const INSERT = 'INSERT';
    const UPDATE = 'UPDATE';
    const DELETE = 'DELETE';
    const SELECT = 'SELECT';

    /**
     *
     * @var Model
     */
    protected $model;
    protected $result;

    /**
     *
     * @var PDOStatement
     */
    protected $statement;
    protected $queryType;

    function __construct($modelClass, PDOStatement $statement, $result = null)
    {
        $this->model = $modelClass;
        $this->statement = $statement;
        $this->result = $result;
    }

    /**
     * 
     * @return type
     */
    public function getStatement()
    {
        return $this->statement;
    }

    public function getModelClass()
    {
        return $this->model;
    }

    public function setModelClass($model)
    {
        $this->model = $model;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getQueryType()
    {
        if (!$this->queryType) {
            $sql = trim($this->statement->queryString);
            preg_match('/^(\w+)/', $sql, $matches);
            $command = strtolower($matches[0]);
            switch ($command) {
                case 'select':
                    $this->queryType = self::SELECT;
                    break;
                case 'insert':
                    $this->queryType = self::INSERT;
                    break;
                case 'create':
                    $this->queryType = self::INSERT;
                    break;
                case 'update':
                    $this->queryType = self::UPDATE;
                    break;
                case 'delete':
                    $this->queryType = self::DELETE;
                    break;
                default :
                    $this->queryType = null;
            }
        }
        return $this->queryType;
    }

}