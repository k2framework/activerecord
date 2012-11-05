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
 * Clase que maneja el pool de conexiones
 *
 * @category   Kumbia
 * @package    DbPool
 * @copyright  Copyright (c) 2005-2009 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace ActiveRecord\DbPool;

use \PDO;
use ActiveRecord\Config\Config;
use ActiveRecord\Config\Parameters;

/**
 * \ActiveRecord\DbPool
 *
 * Clase que maneja el pool de conexiones
 */
class DbPool
{

    /**
     * Singleton de conexiones a base de datos
     *
     * @var array
     */
    protected static $connections = array();

    /**
     * Attributos PDO usados en las conexiones
     *
     * @var array
     */
    protected static $attributes = array();

    /**
     * Realiza una conexión directa al motor de base de datos
     * usando el driver de Kumbia
     *
     * @param string $connection conexion a la base de datos en databases.ini
     * @param boolean $new nueva conexion
     * @return \PDO
     */
    public static function factory(Parameters $config)
    {
        //Si existe la conexion singleton
        if (isset(self::$connections[$config->getId()])) {
            return self::$connections[$config->getId()];
        }

        // check for PDO extension
        if (!extension_loaded('pdo')) {
            throw new KumbiaException('La extensión PDO es requerida por este adaptador, pero la extensión no esta cargada');
        }

        try {
            // conecta con pdo
            self::$connections[$config->getId()] = new PDO(
                            $config->getType() . ':host=' . $config->getHost() . ';dbname=' . $config->getDbName(),
                            $config->getUsername(),
                            $config->getPassword(), self::$attributes);

            self::$connections[$config->getId()]
                    ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return self::$connections[$config->getId()];
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage());
        }
    }

    /**
     * Establece los atributos PDO de las conexiones
     *
     * @param array $attributes
     */
    public static function setAttributes(array $attributes)
    {
        self::$attributes = $attributes;
    }

}
