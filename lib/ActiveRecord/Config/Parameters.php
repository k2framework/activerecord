<?php

/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://wiki.kumbiaphp.com/Licencia
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@kumbiaphp.com so we can send you a copy immediately.
 *
 * Implementacion del patron de diseño ActiveRecord
 *
 * @category   Kumbia
 * @package    ActiveRecord
 * @subpackage Config
 * @copyright  Copyright (c) 2005-2009 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace ActiveRecord\Config;

/**
 * \ActiveRecord\Config\Parameters
 *
 * Describe los parametros de conexión
 *
 * @author maguirre
 */
class Parameters
{

    /**
     * Tipo de adaptador
     */
    protected $type;

    /**
     * Id de la conexión
     *
     * @var string
     */
    protected $id;

    /**
     * Usuario de la conexión
     */
    protected $username;

    /**
     * Password de la conexión
     */
    protected $password;

    /**
     * Host de la conexión
     */
    protected $host = 'localhost';

    /**
     * Puerto de la conexión
     */
    protected $port;

    /**
     * Nombre de la base de datos
     */
    protected $dbName;

    /**
     * Charset de la conexión
     */
    protected $charset;

    /**
     * Constructor de la clase
     *
     * @param string $id
     * @param array $config
     */
    function __construct($id, array $config = array())
    {
        $this->id = $id;
        isset($config['type']) && $this->type = $config['type'];
        isset($config['username']) && $this->username = $config['username'];
        isset($config['password']) && $this->password = $config['password'];
        isset($config['name']) && $this->dbName = $config['name'];
        isset($config['host']) && $this->host = $config['host'];
        isset($config['port']) && $this->port = $config['port'];
        isset($config['charset']) && $this->charset = $config['charset'];
    }

    /**
     * Obtiene el id de la conexión
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Obtiene el usuario con el que se autenticará en el sistema de base de datos
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Establece el usuario con el que se autenticará en el sistema de base de datos
     *
     * @param string $username
     * @return \ActiveRecord\Parameters
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Obtiene el password con el que se autenticará en el sistema de base de datos
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Establece el password con el que se autenticará en el sistema de base de datos
     *
     * @param string $password
     * @return \ActiveRecord\Parameters
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Obtiene el puerto TCP donde se realiza la conexión (en caso de aplicar)
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Establece el puerto TCP donde se realiza la conexión (en caso de aplicar)
     *
     * @param int $port
     * @return \ActiveRecord\Parameters
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * Obtiene el nombre de la base de datos
     *
     * @return string
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * Establece el nombre de la base de datos
     *
     * @param string $dbName
     * @return \ActiveRecord\Parameters
     */
    public function setDbName($dbName)
    {
        $this->dbName = $dbName;
        return $this;
    }

    public function getHost()
    {
        return $this->host;
    }

    /**
     * Establece el host de la conexión
     *
     * @param string $host
     * @return \ActiveRecord\Parameters
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Obtiene el tipo de adaptador asociado a la conexión
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Establece el tipo de motor
     *
     * @param string $type
     * @return \ActiveRecord\Parameters
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Obtiene el charset de la conexión
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Establece el charset asociado a la conexión
     *
     * @param string $charset
     * @return \ActiveRecord\Parameters
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * Convierte el objeto en un array
     *
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

}
