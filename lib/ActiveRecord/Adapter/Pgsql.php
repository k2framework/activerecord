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
 * Clase base para los adaptadores de Base de Datos
 *
 * @category   Kumbia
 * @package    ActiveRecord
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2009 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace ActiveRecord\Adapter;

use ActiveRecord\Adapter\Adapter;
use ActiveRecord\Metadata\Metadata;

/**
 * \ActiveRecord\Adapter\Adapter\Pgsql
 *
 * Adaptador para conectarse a bases de datos PostgreSQL
 */
class Pgsql extends Adapter
{

    /**
     * Obtiene la metadata de una Tabla
     *
     * @param string $schema
     * @param string $table
     * @return Rows
     */
    public function describe($table, $schema = null)
    {
        $sql = "SELECT c.column_name as name,
                CASE
                WHEN ct.constraint_type='PRIMARY KEY' THEN 'PRI'
                WHEN ct.constraint_type='UNIQUE' THEN 'UNI'
                WHEN ct.constraint_type='FOREIGN KEY' THEN 'FK'
                WHEN ct.constraint_type='CHECK' THEN 'CHK'
                ELSE '' END AS Index,
                c.column_default as Default,
                c.is_nullable as Null,
                c.udt_name as Type,
                CASE
                WHEN c.character_maximum_length is null THEN (c.numeric_precision) ELSE c.character_maximum_length END as length
                FROM information_schema.columns c
                LEFT JOIN information_schema.constraint_column_usage cu ON
                cu.table_catalog = c.table_catalog AND cu.table_schema = c.table_schema AND cu.table_name = c.table_name
                AND cu.column_name = c.column_name
                LEFT JOIN information_schema.table_constraints ct ON
                ct.constraint_name = cu.constraint_name
                WHERE c.table_catalog = :database AND c.table_schema = :schema AND c.table_name = :table
                ORDER BY c.ordinal_position";
        try {
            $prepare = $this->prepare($sql);
            //ejecutando la consulta preparada
            $results = $prepare->execute(array('database' => 'test', 'schema' => 'public', 'table' => 'prueba'));
            if ($results) {
                $metadata = new Metadata();
                while ($field = $prepare->fetchObject()) {
                    //Nombre del Campo
                    $attribute = $metadata->attribute($field->name);
                    //alias
                    $attribute->alias = ucwords(strtr($field->name, '_-', '  '));
                    //valor por defecto
                    if (!is_null($field->default)) {
                        if (strpos($field->default, 'nextval(') !== FALSE) {
                            $attribute->autoIncrement = TRUE;
                        } elseif ($field->type == 'serial' || $field->type == 'bigserial') {
                            $attribute->autoIncrement = TRUE;
                        } else {
                            $attribute->default = $field->default;
                        }
                    }
                    //puede ser null?
                    if ($field->null == 'NO') {
                        $attribute->notNull = TRUE;
                    }
                    //Relaciones
                    if (substr($field->name, strlen($field->name) - 3, 3) == '_id') {
                        $attribute->alias = ucwords(strtr($field->name, '_-', '  '));
                    }
                    //tipo de dato
                    $attribute->type = $field->type;
                    //longitud
                    $attribute->length = $field->length;
                    //indices
                    switch ($field->index) {
                        case 'PRI':
                            $metadata->setPK($field->name);
                            $attribute->PK = TRUE;
                            break;
                        case 'FK':
                            $metadata->setFK($field->name);
                            $attribute->FK = TRUE;
                            break;
                        case 'UNI':
                            $attribute->unique = TRUE;
                            break;
                    }
                }
            }
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage());
        }
        return $metadata;
    }

}
