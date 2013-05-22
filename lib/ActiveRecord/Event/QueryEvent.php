<?php

namespace ActiveRecord\Event;

class QueryEvent extends Event
{

    const INSERT = 'INSERT';
    const UPDATE = 'UPDATE';
    const DELETE = 'DELETE';
    const SELECT = 'SELECT';

    /**
     *
     * @var \PDOStatement
     */
    protected $statement;
    protected $queryType;

    function __construct($modelClass, \PDOStatement $statement, $result = null)
    {
        $this->statement = $statement;
        parent::__construct($modelClass, $result, true);
    }

    public function getStatement()
    {
        return $this->statement;
    }

    public function getQueryType()
    {
        if (!$this->queryType) {
            $sql = trim($this->statement->queryString);
            preg_match('/^(\w+)/', $sql, $matches);
            $command = strtolower($matches[0]);
            switch ($command) {
                case 'insert':
                    $this->queryType = self::SELECT;
                    break;
                case 'create':
                    $this->queryType = self::INSERT;
                    break;
                case 'update':
                    $this->queryType = self::UPDATE;
                    break;
                case 'delete':
                    $this->queryType = self::DELETE;
                    break;
                default :
                    $this->queryType = null;
            }
        }
        return $this->queryType;
    }

}