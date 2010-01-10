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
 */
/**
 * @see Metadata
 */
require CORE_PATH . 'libs/ActiveRecord/db_pool/metadata.php';

/** Implementación de Modelo
 * 
 * @category   Kumbia
 * @package    ActiveRecord
 * @copyright  Copyright (c) 2005-2010 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
class KumbiaModel
{
    /**
     * Instancias de metadata de modelos
     *
     * @var array
     **/
    private static $_metadata = array();
    
    /**
     * Obtiene la metatada de un modelo
     *
     * @return Metadata
     **/
    public static function metadata($model)
    {
        if(!isset(self::$_metadata[$model])) {
            self::$_metadata[$model] = new Metadata();
        }
        
        return self::$_metadata[$model];
    }
}