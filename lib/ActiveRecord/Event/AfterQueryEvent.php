<?php

namespace ActiveRecord\Event;

use ActiveRecord\PDOStatement;

/**
 * Description of BeforeEvent
 *
 * @author maguirre
 */
class AfterQueryEvent extends BeforeQueryEvent
{

    /**
     *
     * @var PDOStatement 
     */
    protected $statement;

    function __construct(PDOStatement $statement, $parameters = array())
    {
        $this->statement = $statement;
        parent::__construct($statement->queryString, $parameters);
    }

    /**
     * @return PDOStatement 
     */
    public function getStatement()
    {
        return $this->statement;
    }

    public function getResult()
    {
        return $this->statement->getResult();
    }

}
