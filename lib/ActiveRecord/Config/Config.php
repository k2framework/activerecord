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

use ActiveRecord\Config\Parameters;

/**
 * Description of Config
 *
 * @author maguirre
 */
class Config
{

    /**
     * Bolsa de parametros de conexión
     */
    protected static $parameters = array();

    /**
     * Nombre de la conexión por defecto
     */
    protected static $defaultConectionName;

    /**
     * Agrega un conjunto de parametros a la configuración de ActiveRecord
     *
     * @param \ActiveRecord\Config\Parameters $param
     */
    public static function add(Parameters $param)
    {
        if ( !self::$defaultConectionName ){
            self::setDefault($param->getId());
        }
        if (!self::has($param->getId())) {
            self::$parameters[$param->getId()] = $param;
        }
    }

    /**
     * Verifica si existe unos parametros de conexión apartir de su Id
     *
     * @param string $id
     */
    public static function has($id)
    {
        return isset(self::$parameters[$id]);
    }

    /**
     * Obtiene un conjunto de parametros de conexión apartir de su id
     *
     * @param type $id
     * @return Parameters
     */
    public static function get($id)
    {
        return self::has($id) ? self::$parameters[$id] : NULL;
    }

    /**
     * Verifica si existe al menos una conexión definida
     *
     * @return boolean
     */
    public static function initialized()
    {
        return count(self::$parameters) > 0;
    }

    /**
     * Establece el id de la conexión por defecto
     *
     * @param string $id
     */
    public static function setDefault($id)
    {
        self::$defaultConectionName = $id;
    }

    /**
     * Devuelve el nombre de la conexión por defecto
     *
     * @return string
     */
    public static function getDefault()
    {
        return self::$defaultConectionName;
    }

}