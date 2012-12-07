<?php

namespace ActiveRecord\Event;

use ActiveRecord\Model;
use KumbiaPHP\EventDispatcher\Event as Base;

class Event extends Base
{

    /**
     *
     * @var Model
     */
    protected $model;
    protected $result;
    protected $hasResult;

    function __construct(Model $model, $result = null, $hasResult = false)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setModel(Model $model)
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