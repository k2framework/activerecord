<?php

namespace ActiveRecord\Event;

use ActiveRecord\PDOStatement;

class QueryEvent extends Event
{

    const INSERT = 'INSERT';
    const UPDATE = 'UPDATE';
    const DELETE = 'DELETE';
    const SELECT = 'SELECT';

    public function getQueryType()
    {
        if (!$this->queryType) {
            $sql = trim($this->statement->queryString);
            preg_match('/^(\w+)/', $sql, $matches);
            $command = strtolower($matches[0]);
            switch ($command) {
                case 'select':
                    $this->queryType = self::SELECT;
                    break;
                case 'insert':
                    $this->queryType = self::INSERT;
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