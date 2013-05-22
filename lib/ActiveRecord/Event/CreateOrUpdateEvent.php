<?php

namespace ActiveRecord\Event;

use ActiveRecord\Model;

class CreateOrUpdateEvent extends Event
{

    protected $data;

    function __construct($model, array $data = array())
    {
        $this->data = $data;
        parent::__construct($model);
    }

}