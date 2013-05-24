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
use ActiveRecord\Paginator\Result;

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
     * @return Result
     * */
    public static function paginate(DbQuery $query, $page, $per_page, $fetchMode = Model::FETCH_MODEL)
    {
        $arrayQuery = $query->getSqlArray() + array('columns' => '*');

        $model = $query->getModelClass();

        $numItems = $model::count($query);

        $page = (int) $page;
        $per_page = (int) $per_page;
        $offset = ($page - 1) * $per_page;

        $query->select($arrayQuery['columns'])->limit($per_page)->offset($offset);

        $items = $model::query($query, $fetchMode)->fetchAll();

        return new Result($items, $numItems, $page, $per_page);
    }

}
