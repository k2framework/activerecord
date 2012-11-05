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
 * @category   Kumbia
 * @package    ActiveRecord
 * @subpackage Paginator
 * @copyright  Copyright (c) 2005-2012 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace ActiveRecord\Paginator;

use ActiveRecord\Model;
use ActiveRecord\Query\DbQuery;

/**
 * ActiveRecord\Paginator\Paginator
 *
 * Componente para paginar. Soporta arrays y modelos
 */
class Paginator
{

    /**
     * paginador
     *
     * page: número de página a mostrar (por defecto la página 1)
     * per_page: cantidad de registros por página (por defecto 10 registros por página)
     *
     * Para páginacion por array:
     *  Parámetros sin nombre en orden:
     *    Parámetro1: array a páginar
     *
     * Para páginacion de modelo:
     *  Parámetros sin nombre en orden:
     *   Parámetro1: nombre del modelo o objeto modelo
     *   Parámetro2: condición de busqueda
     *
     * Parámetros con nombre:
     *  conditions: condición de busqueda
     *  order: ordenamiento
     *  columns: columnas a mostrar
     *
     * Retorna un PageObject que tiene los siguientes atributos:
     *  next: número de página siguiente, si no hay página siguiente entonces es FALSE
     *  prev: numero de página anterior, si no hay página anterior entonces es FALSE
     *  current: número de página actual
     *  total: total de páginas que se pueden mostrar
     *  items: array de registros de la página
     *  count: Total de registros
     *  per_page: cantidad de registros por página
     *
     * @example
     *  $page = paginate($array, 'per_page: 5', "page: $page_num"); <br>
     *  $page = paginate('usuario', 'per_page: 5', "page: $page_num"); <br>
     *  $page = paginate('usuario', 'sexo="F"' , 'per_page: 5', "page: $page_num"); <br>
     *  $page = paginate('Usuario', 'sexo="F"' , 'per_page: 5', "page: $page_num"); <br>
     *  $page = paginate($this->Usuario, 'conditions: sexo="F"' , 'per_page: 5', "page: $page_num"); <br>
     *
     * @return object
     * */
    public static function paginate(Model $model, DbQuery $query, $page, $per_page)
    {
        $arrayQuery = $query->getSqlArray() + array('columns' => '*');

        $numItems = $model::count();
        $offset = ($page - 1) * $per_page;

        $query->columns($arrayQuery['columns'])->limit($per_page)->offset($offset);

        $items = $model->query($query)->fetchAll();

        $object = new \stdClass();

        $object->next = ($offset + $per_page) < $numItems ? ($page + 1) : FALSE;
        $object->prev = ($page > 1) ? ($page - 1) : FALSE;
        $object->current = $page;
        $object->total = ceil($numItems / $per_page);
        $object->count = $numItems;
        $object->per_page = $per_page;

        $object->items = $items;

        return $object;
    }

}
