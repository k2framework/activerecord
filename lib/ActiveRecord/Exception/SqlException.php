<?php

namespace ActiveRecord\Exception;

use ActiveRecord\Exception\ActiveRecordException;

/**
 * Description of ActiveRecordException
 *
 * @author maguirre
 */
class SqlException extends ActiveRecordException
{

    function __construct(\Exception $e, \PDOStatement $st = null, array $parameters = null)
    {
        parent::__construct($e->getMessage());

        if ($st) {
            $this->message .="<pre>Consulta: {$st->queryString}</pre>";
            $this->message .="<pre>Parametros: " . print_r($parameters, true) . "</pre>";
        }
    }

}

