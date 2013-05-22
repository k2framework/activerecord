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

    function __construct($modelClass, $result = null)
    {
        $this->model = $modelClass;
        $this->result = $result;
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

}