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

use ActiveRecord\Metadata\Attribute;

/**
 * \ActiveRecord\Metadata\Metadata
 *
 * Obtiene y almacena los meta-datos de los modelos
 */
class Metadata
{

    /**
     * Atributos de modelo (metadata)
     *
     * @var array
     * */
    private $attributes = array();

    /**
     * Lista de atributos
     *
     * @var array
     */
    private $_attributesList = array();

    /**
     * Clave primaria
     *
     * @var string
     */
    private $_PK = NULL;

    /**
     * Claves foraneas
     *
     * @var array
     */
    private $_FK = NULL;

    /**
     * Obtiene la metadata de un atributo
     *
     * @param string $attribute nombre de atributo
     * @return Attribute
     * */
    public function attribute($attribute = NULL)
    {
        if (!isset($this->_attributes[$attribute])) {
            $this->_attributes[$attribute] = new Attribute();
            $this->_attributesList[] = $attribute;
        }
        return $this->_attributes[$attribute];
    }

    /**
     * Obtiene los atributos
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * Obtiene la lista de atributos
     *
     * @return array
     */
    public function getAttributesList()
    {
        return $this->_attributesList;
    }

    /**
     * Asigna la clave primaria
     *
     * @param string $pk
     * */
    public function setPK($pk = NULL)
    {
        $this->_PK = $pk;
    }

    /**
     * Obtiene la clave primaria
     *
     * @return string
     * */
    public function getPK()
    {
        return $this->_PK;
    }

    /**
     * Asigna las claves foraneas
     *
     * @param array $fk
     * */
    public function setFK($fk = NULL)
    {
        $this->_FK = $fk;
    }

    /**
     * Obtiene las claves foraneas
     *
     * @return array | NULL
     * */
    public function getFK()
    {
        return $this->_FK;
    }

}
