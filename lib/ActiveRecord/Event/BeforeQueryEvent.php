<?php

namespace ActiveRecord\Event;

use KumbiaPHP\EventDispatcher\Event;

/**
 * Description of BeforeEvent
 *
 * @author maguirre
 */
class BeforeQueryEvent extends Event
{

    const INSERT = 'INSERT';
    const UPDATE = 'UPDATE';
    const DELETE = 'DELETE';
    const SELECT = 'SELECT';

    protected $query;
    protected $parameters;
    protected $queryType;

    public function __construct($query, $parameters = array())
    {
        $this->query = trim($query);
        $this->parameters = $parameters;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    public function getQueryType($generate = true)
    {
        if ($generate && !$this->queryType) {
            $query = strtoupper($this->query);
            if (0 === strpos($query, 'SELECT')) {
                $this->queryType = self::SELECT;
            } elseif (0 === strpos($query, 'INSERT INTO')) {
                $this->queryType = self::INSERT;
            } elseif (0 === strpos($query, 'UPDATE')) {
                $this->queryType = self::UPDATE;
            } elseif (0 === strpos($query, 'DELETE')) {
                $this->queryType = self::DELETE;
            }
        }
        return $this->queryType;
    }

}
