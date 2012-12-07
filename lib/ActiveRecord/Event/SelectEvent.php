<?php

namespace ActiveRecord\Event;

use ActiveRecord\Model;
use ActiveRecord\Query\DbQuery;

class SelectEvent extends Event
{

    const INSERT = 'INSERT';
    const UPDATE = 'UPDATE';
    const DELETE = 'DELETE';
    const SELECT = 'SELECT';

    /**
     *
     * @var DbQuery 
     */
    protected $dbQuery;

    function __construct(Model $model, DbQuery $dbQuery, $result = null, $hasResult = false)
    {
        $this->dbQuery = $dbQuery;
        parent::__construct($model, $result, $hasResult);
    }

    public function getParameters()
    {
        return $this->dbQuery->getBind();
    }

    public function getDbQuery()
    {
        return $this->dbQuery;
    }

    public function getQueryType()
    {
        if (!$this->queryType) {
            $sql = $this->dbQuery->getSqlArray();
            $query = strtoupper($this->query);
            switch ($sql['command']) {
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