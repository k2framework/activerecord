<?php

namespace ActiveRecord\Event;

use ActiveRecord\Model;
use ActiveRecord\PDOStatement;
use K2\EventDispatcher\Event as Base;

class Event extends Base
{

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

}