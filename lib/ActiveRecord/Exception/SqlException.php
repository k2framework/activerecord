<?php

namespace ActiveRecord\Exception;

use ActiveRecord\Exception\ActiveRecordException;
use ActiveRecord\PDOStatement;

/**
 * Description of ActiveRecordException
 *
 * @author maguirre
 */
class SqlException extends ActiveRecordException
{

    function __construct(\Exception $e, PDOStatement $st = null, array $parameters = null)
    {
        parent::__construct($e->getMessage());

        if ($st) {
            $this->message .="<pre>Consulta: {$st->getSqlQuery()}</pre>";
        }
    }

}

