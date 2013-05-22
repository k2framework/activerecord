<?php

namespace ActiveRecord;

use \PDOStatement as Base;

/**
 * Description of PDOStatement
 *
 * @author manuel
 */
class PDOStatement extends Base
{

    protected $parameters;

    public function execute($input_parameters = null)
    {
        $this->parameters = $input_parameters;

        $this->result = parent::execute($input_parameters);

        return $this;
    }

    /**
     * Devuelve el sql como se ejecutaria en el servidor de base de datos
     */
    public function getSqlQuery()
    {
        
    }

}