<?php

namespace ActiveRecord\Event;

use ActiveRecord\Model;
use K2\EventDispatcher\Event as Base;

class Event extends Base
{

    /**
     *
     * @var Model
     */
    protected $model;
    protected $result;
    protected $hasResult;

    function __construct($modelClass, $result = null, $hasResult = false)
    {
        $this->model = $modelClass;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function hasResult()
    {
        return $this->hasResult;
    }

}