<?php

require_once __DIR__ . '/autoload.php';

use ActiveRecord\Query\DbQuery;

class DbQueryTest extends PHPUnit_Framework_TestCase
{

    public function testReturns()
    {
        $query = new DbQuery();

        //$this->assertEquals($query, $actual);
        $this->assertInstanceOf(get_class($query), $query->columns('*'));
        $this->assertInstanceOf(get_class($query), $query->table('table'));
        $this->assertInstanceOf(get_class($query), $query->where('campo = :valor'));
        $this->assertInstanceOf(get_class($query), $query->whereOr('campo = :valor'));
        $this->assertInstanceOf(get_class($query), $query->join('table2', 'conditions'));
        $this->assertInstanceOf(get_class($query), $query->fullJoin('table2', 'conditions'));
        $this->assertInstanceOf(get_class($query), $query->leftJoin('table3', 'conditions'));
        $this->assertInstanceOf(get_class($query), $query->rightJoin('table4', 'conditions'));
        $this->assertInstanceOf(get_class($query), $query->rightJoin('table5', 'conditions'));
        $this->assertInstanceOf(get_class($query), $query->limit(5));
        $this->assertInstanceOf(get_class($query), $query->order('column ASC'));
        $this->assertInstanceOf(get_class($query), $query->offset(5));
        $this->assertInstanceOf(get_class($query), $query->group('columns'));
        $this->assertInstanceOf(get_class($query), $query->having('conditions'));
        $this->assertInstanceOf(get_class($query), $query->schema('schema'));
        $this->assertInstanceOf(get_class($query), $query->bind(array()));
        $this->assertInstanceOf(get_class($query), $query->bindValue('index', 'value'));
    }

    public function testQueries()
    {
        $query = new DbQuery();

        $query->select('*')
                ->table('usuarios')
                ->where('usuarios.id = :id')
                ->bindValue('id', 100);

        $sqlArray = array(
            'command' => 'select',
            'columns' => '*',
            'table' => 'usuarios',
            'where' => array('(usuarios.id = :id)'),
            'bind' => array(
                ':id' => 100
            )
        );

        $this->assertEquals($sqlArray, $query->getSqlArray());
        $this->assertEquals($sqlArray['bind'], $query->getBind());

        $query->whereOr('usuarios.login = :login')
                ->bindValue('login', 'admin');

        $sqlArray['where'][] = ' OR (usuarios.login = :login)';
        $sqlArray['bind'][':login'] = 'admin';


        $this->assertEquals($sqlArray, $query->getSqlArray());
        $this->assertEquals($sqlArray['bind'], $query->getBind());

        $query->bind(array(
            'email' => 'admin@admin.com'
        ));

        $sqlArray['bind'][':email'] = 'admin@admin.com';

        $this->assertEquals($sqlArray, $query->getSqlArray());

        $query = new DbQuery();

        $query->select('*')
                ->where('usuarios.id = :id')
                ->whereOr('usuarios.login = :login')
                ->table('usuarios')
                ->bind(array(
                    'id' => 100,
                    'login' => 'admin',
                    'email' => 'admin@admin.com',
                ));

        $this->assertEquals($sqlArray, $query->getSqlArray());
    }

}
