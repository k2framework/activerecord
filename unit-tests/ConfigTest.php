<?php

require_once 'autoload.php';

use \ActiveRecord\Config\Config;
use \ActiveRecord\Config\Parameters;

class ConfigTest extends PHPUnit_Framework_TestCase
{
	public function testParameters()
	{
		//Configuración Vacia
		$parameters = new Parameters('id');
		$this->assertEquals($parameters->getId(), 'id');
		$this->assertEquals($parameters->getUserName(), null);
		$this->assertEquals($parameters->getPassword(), null);
		$this->assertEquals($parameters->getHost(), 'localhost');
		$this->assertEquals($parameters->getPort(), null);
		$this->assertEquals($parameters->getDbName(), null);
		$this->assertEquals($parameters->getType(), null);
		$this->assertEquals($parameters->getCharset(), null);

		//Configuración con Datos
		$parameters = new Parameters('otro-id', array(
			"type"     => "mysql",
			'username' => "root",
			"password" => "123456",
			"host"     => "127.0.0.1",
			"port"     => "3306",
			"name"     => "mi_base_de_datos",
			"charset"  => "utf8"
		));
		$this->assertEquals($parameters->getId(), 'otro-id');
		$this->assertEquals($parameters->getType(), "mysql");
		$this->assertEquals($parameters->getUserName(), "root");
		$this->assertEquals($parameters->getPassword(), "123456");
		$this->assertEquals($parameters->getHost(), '127.0.0.1');
		$this->assertEquals($parameters->getPort(), "3306");
		$this->assertEquals($parameters->getDbName(), "mi_base_de_datos");
		$this->assertEquals($parameters->getCharset(), "utf8");

		//Fluent interface
		$parameters = new Parameters('fluent-id');
		$parameters->setType('mysql')
				->setUserName('root')
				->setPassword('123456')
				->setHost('127.0.0.1')
				->setPort('3306')
				->setDbName("mi_base_de_datos")
				->setCharset('utf8');

		$this->assertEquals($parameters->getId(), 'fluent-id');
		$this->assertEquals($parameters->getUserName(), "root");
		$this->assertEquals($parameters->getPassword(), "123456");
		$this->assertEquals($parameters->getHost(), '127.0.0.1');
		$this->assertEquals($parameters->getPort(), "3306");
		$this->assertEquals($parameters->getDbName(), "mi_base_de_datos");
		$this->assertEquals($parameters->getCharset(), "utf8");

	}

	public function testConfig()
	{

		$this->assertFalse(Config::initialized());

		$parameters1 = new Parameters('primer-id', array(
			"type"     => "mysql",
			'username' => "root",
			"password" => "123456",
			"host"     => "127.0.0.1",
			"port"     => "3306",
			"name"     => "mi_base_de_datos",
			"charset"  => "utf8"
		));

		Config::add($parameters1);

		$this->assertTrue(Config::initialized());
		$this->assertTrue(Config::has('primer-id'));
		$this->assertFalse(Config::has('segundo-id'));

		$this->assertEquals(Config::get('primer-id'), $parameters1);
		$this->assertEquals(Config::get('segundo-id'), null);

		$this->assertEquals(Config::getDefaultId(), 'primer-id');
		$this->assertEquals(Config::getDefault(), $parameters1);

		$parameters2 = new Parameters('segundo-id', array(
			"type"     => "mysql",
			'username' => "root",
			"password" => "hello",
			"host"     => "127.0.0.1",
			"name"     => "otra_bd",
			"charset"  => "utf8"
		));

		Config::add($parameters2);

		$this->assertTrue(Config::has('primer-id'));
		$this->assertTrue(Config::has('segundo-id'));

		$this->assertEquals(Config::get('primer-id'), $parameters1);
		$this->assertEquals(Config::get('segundo-id'), $parameters2);

	}

}
