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
 * Clase para consultas SQL para PostgreSQL
 *
 * @category   Kumbia
 * @package    ActiveRecord
 * @subpackage Metadata
 * @copyright  Copyright (c) 2005-2009 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace ActiveRecord\Metadata;

/**
 * \ActiveRecord\Metadata\Attribute
 *
 * Describe cada atributo de un modelo
 */
class Attribute
{
    /**
     * Alias del campo
     */
    public $alias = NULL;

    /**
     * Tipo de dato de cara a la APP
     */
    public $type = NULL;

    /**
     * Valor por defecto del campo
     */
    public $default = NULL;

    /**
     * Longitud del Campo
     */
    public $length = 50;

    /**
     * Indica si es NULL el campo
     */
    public $notNull = TRUE;

    /**
     * Indica si es PK el campo
     */
    public $PK = FALSE;

    /**
     * Indica si es FK el campo
     */
    public $FK = FALSE;

    /**
     * Indica si es Unique el campo
     */
    public $unique = FALSE;

    /**
     * Campo con secuencias (serial o auto-increment)
     */
    public $autoIncrement = FALSE;

    /**
     * Formato para fechas
     */
    public $format = NULL;

}