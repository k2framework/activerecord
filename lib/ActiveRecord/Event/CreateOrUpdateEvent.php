<?php

namespace ActiveRecord\Event;

use ActiveRecord\PDOStatement;

class CreateOrUpdateEvent extends Event
{

    protected $data;

    function __construct($modelClass, PDOStatement $statement, $data)
    {
        $this->data = $data;
        parent::__construct($modelClass, $statement);
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

}