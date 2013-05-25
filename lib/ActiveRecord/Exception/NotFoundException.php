<?php

namespace ActiveRecord\Exception;

use ActiveRecord\Exception\ActiveRecordException;

class NotFoundException extends ActiveRecordException
{

    public function __construct($message)
    {
        parent::__construct($message, 404);
    }

}
