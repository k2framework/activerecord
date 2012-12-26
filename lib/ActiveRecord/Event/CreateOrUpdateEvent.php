<?php

namespace ActiveRecord\Event;

use ActiveRecord\Model;

class CreateOrUpdateEvent extends Event
{

    protected $data;

    function __construct(Model $model, array $data = array())
    {
        $this->data = $data;
        parent::__construct($model);
    }

}