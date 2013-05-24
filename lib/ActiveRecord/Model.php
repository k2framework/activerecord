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
 * @copyright  Copyright (c) 2005-2009 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace ActiveRecord;

use \PDO;
use ActiveRecord\Relations;
use ActiveRecord\Event\Event;
use ActiveRecord\Event\Events;
use ActiveRecord\Query\DbQuery;
use ActiveRecord\Adapter\Adapter;
use ActiveRecord\Metadata\Metadata;
use ActiveRecord\Paginator\Paginator;
use ActiveRecord\Exception\SqlException;
use ActiveRecord\Event\CreateOrUpdateEvent;
use ActiveRecord\Exception\NotFoundException;
use ActiveRecord\Exception\ActiveRecordException;

/**
 * ActiveRecord Clase para el Mapeo Objeto Relacional
 *
 * Active Record es un enfoque al problema de acceder a los datos de una
 * base de datos en forma orientada a objetos. Una fila en la
 * tabla de la base de datos (o vista) se envuelve en una clase,
 * de manera que se asocian filas únicas de la base de datos
 * con objetos del lenguaje de programación usado.
 * Cuando se crea uno de estos objetos, se añade una fila a
 * la tabla de la base de datos. Cuando se modifican los atributos del
 * objeto, se actualiza la fila de la base de datos.
 */
class Model
{
    /**
     * Obtener datos cargados en objeto del Modelo
     *
     */

    const FETCH_MODEL = 'model';

    /**
     * Obtener datos cargados en objeto
     *
     */
    const FETCH_OBJ = 'obj';

    /**
     * Obtener datos cargados en array
     *
     */
    const FETCH_ARRAY = 'array';

    /**
     * Conexion a base datos que se utilizara
     *
     * @var strings
     */
    protected static $connection = null;

    /**
     * Constructor de la class
     *
     * @param array $data
     */
    public final function __construct($data = null)
    {
        if (is_array($data)) {
            $this->dump($data);
        }
        $this->initialize();
        if (!Relations::has(get_called_class())) {
            Relations::setLoaded(get_called_class());
            $this->createRelations();
        }
    }

    /**
     * Obtiene la metatada de un modelo
     *
     * @return Metadata
     */
    public static function metadata()
    {
        static $metadata;

        if (!$metadata) {
            $metadata = Adapter::factory(static::$connection)
                    ->describe(static::table(), null); //el esquema por ahora null
        }

        return $metadata;
    }

    /**
     * Carga el array como atributos del objeto
     *
     * @param array $data
     */
    public function dump($data)
    {
        $validFields = static::metadata()->getAttributesList();
        foreach ($data as $k => $v) {
            if (in_array($k, $validFields)) {
                $this->$k = $v;
            }
        }
    }

    /**
     * Método que se puede sobreescribir para hacer configuraciónes en el modelo,
     * Este método será llamado por el constructor de la clase. 
     */
    protected function initialize()
    {
        
    }

    /**
     * Este método es llamado por el constructor despues de llamar al método
     * initialize, la diferencia de este método con initialize es que solo es
     * llamado 1 vez en la petición, para cargar las validaciones de todas
     * las instancias para un modelo especifico. 
     */
    protected function createRelations()
    {
        
    }

    /**
     * Método que será ejecutado antes de hacer un INSERT en la BD
     *
     * @return boolean
     */
    protected function beforeCreate()
    {
        
    }

    /**
     * Método que será ejecutado despues de hacer un INSERT en la BD
     *
     * @return boolean
     */
    protected function afterCreate()
    {
        
    }

    /**
     * Método que será ejecutado antes de hacer un UPDATE de un registro en la BD
     *
     * @return boolean
     */
    protected function beforeUpdate()
    {
        
    }

    /**
     * Método que será ejecutado antes de hacer un INSERT ó UPDATE en la BD
     *
     * @return boolean
     */
    protected function beforeSave()
    {
        
    }

    /**
     * Callback para realizar validaciones
     *
     * @return boolean
     */
    protected function validate($update = false)
    {
        
    }

    /**
     * Método que será ejecutado despues de hacer un UPDATE en la BD
     *
     * @return boolean
     */
    protected function afterUpdate()
    {
        
    }

    /**
     * Método que será ejecutado despues de hacer un INSERT ó UPDATE en la BD
     *
     * @return boolean
     */
    protected function afterSave()
    {
        
    }

    /**
     * Establece el modo de devolución de los datos
     * @param \PDOStatement $sts
     * @param type $fetchMode
     */
    private static function setFetchMode(\PDOStatement $sts, $fetchMode)
    {
        switch ($fetchMode) {
// Obtener arrays
            case static::FETCH_ARRAY:
                $sts->setFetchMode(PDO::FETCH_ASSOC);
                break;

// Obtener instancias de objetos simples
            case static::FETCH_OBJ:
                $sts->setFetchMode(PDO::FETCH_OBJ);
                break;

// Obtener instancias del mismo modelo
            case static::FETCH_MODEL:
            default:
// Instancias de un nuevo modelo, por lo tanto libre de los atributos de la instancia actual
                $sts->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        }
    }

    /**
     * Obtiene/establece el nombre de la tabla que el modelo está representando.
     *
     * @return string
     */
    public static function table($name = null)
    {
        static $table;

        if ($name) {
            $table = $name;
        } elseif (!$table) {
            $table = self::createTableName(get_called_class());
        }

        return $table;
    }

    /**
     * Asigna el esquema para la tabla
     *
     * @param string $schema
     * @return ActiveRecord
     */
    public static function schema($name = null)
    {
        static $schema;

        if ($name) {
            $schema = $name;
        } elseif (!$schema) {
            $schema = null;
        }

        return $schema;
    }

    /**
     * Ejecuta una setencia SQL aplicando Prepared Statement
     *
     * @param string $sql Setencia SQL
     * @param array $params parámetros que seran enlazados al SQL
     * @param string $fetchMode
     * @return \PDOStatement
     */
    public static function sql($sql, $params = null, $fetchMode = self::FETCH_MODEL)
    {
        $statement = null;
        try {
            $statement = Adapter::factory(static::$connection)
                    ->prepare($sql);

            $this->setFetchMode($statement, $fetchMode);

// Ejecuta la consulta
            $statement->execute($params);
            return $statement;
        } catch (\PDOException $e) {
            if ($statement instanceof \PDOStatement) {
                throw new SqlException($e, $statement);
            } else {
                throw $e;
            }
        }
    }

    /**
     * Ejecuta una consulta de dbQuery
     *
     * @param DbQuery $dbQuery Objeto de consulta
     * @param string $fetchMode
     * @return \PDOStatement
     */
    public static function query(DbQuery $dbQuery, $fetchMode = self::FETCH_MODEL)
    {
        static::createQuery();

        $dbQuery->table(static::table())->schema(static::schema());

        $statement = null;
        try {
// Obtiene una instancia del adaptador y prepara la consulta
            $statement = Adapter::factory(static::$connection)
                    ->prepareDbQuery($dbQuery);

// Indica el modo de obtener los datos en el ResultSet
            self::setFetchMode($statement, $fetchMode);

// Ejecuta la consulta
            $statement->execute($dbQuery->getBind());
            return $statement;
        } catch (\PDOException $e) {
            if ($statement instanceof \PDOStatement) {
                throw new SqlException($e, $statement, $dbQuery->getBind());
            } else {
                throw $e;
            }
        }
    }

    /**
     * Crea y devuelve una instancia de la clase DbQuery, la cual nos permite
     * crear consultas de manera orientada a objetos.
     *
     * @return DbQuery
     */
    public static function createQuery()
    {
        $a = get_called_class();
// Crea la instancia de DbQuery
        return static::dbQuery(new DbQuery(get_called_class()));
    }

    /**
     * Efectua una consulta SELECT y devuelve el primer registro encontrado.
     *
     * @param string $fetchMode
     * @return Model
     */
    public static function find($fetchMode = self::FETCH_MODEL)
    {
        $dbQuery = static::dbQuery()->select();

        $statement = static::query($dbQuery, $fetchMode);

        return static::dispatchQueryEvent($statement, $statement->fetch());
    }

    /**
     * Efectua una consulta SELECT y devuelve un arreglo con los registros devueltos.
     *
     * @param string $fetchMode
     * @return array
     */
    public static function findAll($fetchMode = null)
    {
        $dbQuery = static::dbQuery()->select();

        $statement = static::query($dbQuery, $fetchMode);

        return static::dispatchQueryEvent($statement, $statement->fetchAll());
    }

    /**
     * Crea condiciones en el DbQuery a partir de un array
     * @param \ActiveRecord\Query\DbQuery $q
     * @param array $conditions
     */
    protected static function createConditions(DbQuery $q, array $conditions = array())
    {
        $x = 0;
        foreach ($conditions as $column => $value) {
            if (is_array($value)) {
                if (count($value)) {
                    $cond = ':_' . join(",:_", array_keys($value));
                    $q->where("$column IN ($cond)");
                    foreach ($value as $key => $val) {
                        $q->bindValue("_$key", (string) $val);
                    }
                }
            } else {
                $q->where("$column = :v$x")
                        ->bindValue("v$x", $value);
            }
            ++$x;
        }
    }

    /**
     * Busca por una serie de campos pasados como array
     * @param array $conditions
     * @param string $fetchMode
     * @return Model
     */
    public static function findBy(array $conditions = array(), $fetchMode = null)
    {
        self::createConditions(static::createQuery(), $conditions);

        return static::find($fetchMode);
    }

    /**
     * Busca por una serie de campos pasados como array y devuelve todas las coincidencias
     * @param array $conditions
     * @param string $fetchMode
     * @return Model
     */
    public static function findAllBy(array $conditions = array(), $fetchMode = null)
    {
        self::createConditions(static::createQuery(), $conditions);

        return static::findAll($fetchMode);
    }

    /**
     * Buscar por medio de la clave primaria
     *
     * @param string $value
     * @param string $fetchMode
     * @return Model
     */
    public static function findByPK($value, $throw = true, $fetchMode = null)
    {
        $pk = static::metadata()->getPK();

        $query = static::createQuery()
                ->select()
                ->where("$pk = :pk")
                ->bindValue('pk', $value);
// Realiza la busqueda y retorna el objeto ActiveRecord
        $result = static::find($fetchMode);

        if (!$result && $throw) {
            throw new NotFoundException('No existe un registro en ' . static::table() . " con {$pk} = {$value}");
        }

        return $result;
    }

    /**
     * Buscar por medio del id
     *
     * @param string $value
     * @param int $id
     * @return Model
     */
    public static function findByID($id, $throw = true, $fetchMode = null)
    {
        return static::findByPK((int) $id, $throw, $fetchMode);
    }

    /**
     * Obtiene un array de los atributos que corresponden a columnas
     * en la tabla
     *
     * @return array
     */
    private function getTableValues()
    {
        $data = array();

// Itera en cada atributo
        foreach (static::metadata()->getAttributes() as $fieldName => $attr) {

            if (property_exists($this, $fieldName)) {
                if ($this->$fieldName === '') {
                    if (!$attr->default) {
                        $data[$fieldName] = null;
                    }
                } else {
                    $data[$fieldName] = $this->$fieldName;
                }
            } else {
                if (!$attr->default) {
                    $data[$fieldName] = null;
                }
            }
        }

        return $data;
    }

    /**
     * Realiza un insert sobre la tabla
     *
     * @param array $data información a ser guardada
     * @return boolean
     */
    public function create($data = null)
    {
// Si es un array, se cargan los atributos en el objeto
        if (is_array($data)) {
            $this->dump($data);
        }

// Callback antes de crear
        if (false === $this->beforeCreate() || false === $this->beforeSave()) {
            return false;
        }

// Callback de validaciónes
        if (false === $this->validate(false)) {
            return false;
        }

// Nuevo contenedor de consulta
        $dbQuery = static::createQuery();

        $data = $this->getTableValues();

        if (isset($data[static::metadata()->getPK()])) {
            unset($data[static::metadata()->getPK()]);
        }
// Ejecuta la consulta
        if ($statement = static::query($dbQuery->insert($data))) {

// Convenio patron identidad en activerecord si PK es "id"
            if (is_string($pk = static::metadata()->getPK()) && (!isset($this->$pk) || $this->$pk == '')) {
// Obtiene el ultimo id insertado y lo carga en el objeto
                $this->$pk = Adapter::factory(static::$connection)
                                ->pdo()->lastInsertId();
            }

            static::dispatchQueryEvent($statement, $this);

            if (Adapter::getEventDispatcher()->hasListeners(Events::CREATE)) {
                $event = new CreateOrUpdateEvent(get_called_class(), $statement, $this);
                Adapter::getEventDispatcher()->dispatch(Events::CREATE, $event);
            }

// Callback despues de crear
            $this->afterCreate();
            $this->afterSave();
            return true;
        }

        return false;
    }

    /**
     * Realiza un update sobre la tabla
     *
     * @param DbQuery $query objeto con la data a guardar y las condiciones establecidas
     * 
     * @example 
     * 
     * $query = new DbQuery();
     * 
     * $query->update(array('active' => true))->where('role = :r')
     * ->bindValue('r', 'admin');
     * 
     * Usuarios::updateAll($query);
     * 
     * ejecuta: UPDATE usuarios SET active=1 WHERE role = 'admin'
     * 
     * @return int 
     */
    public static function updateAll(DbQuery $query)
    {
// TODO: se debe verificar que el query creado es para actualizar.
// Ejecuta la consulta
        $statement = static::query($query);

        $count = $statement->rowCount();

        static::dispatchQueryEvent($statement, $this);

        return $count;
    }

    /**
     * Realiza un delete sobre la tabla
     *
     * @param DbQuery $query objeto con la data a guardar y las condiciones establecidas
     * 
     * @example 
     * 
     * $query = new DbQuery();
     * 
     * $query->where('role = :r')->bindValue('r', 'admin');
     * 
     * Usuarios::updateAll($query);
     * 
     * ejecuta: DELETE FROM usuarios WHERE role = 'admin'
     * 
     * @return int
     */
    public static function deleteAll(DbQuery $query)
    {
// Ejecuta la consulta
        $statement = static::query($query->delete());

        return static::dispatchQueryEvent($statement, $statement->rowCount());
    }

    /**
     * Cuenta el numero de registros devueltos en una consulta de tipo SELECT
     * @param \ActiveRecord\Query\DbQuery $query
     * @return int
     */
    public static function count(DbQuery $query = null)
    {
        static::dbQuery($query)->columns("COUNT(*) AS n");
        return static::find(static::FETCH_OBJ)->n;
    }

    /**
     * Verifica si existe al menos una fila con las condiciones indicadas
     *
     * @return boolean
     */
    public function existsOne()
    {
        return static::count() > 0;
    }

    /**
     * Establece condicion de busqueda con clave primaria
     *
     * @param DbQuery $dbQuery
     */
    protected function wherePK(DbQuery $dbQuery)
    {
// Obtiene la clave primaria
        $pk = static::metadata()->getPK();

// Si es clave primaria compuesta
        if (is_array($pk)) {
            foreach ($pk as $k) {
                if (!isset($this->$k)) {
                    throw new ActiveRecordException("Debe definir valor para la columna $k de la clave primaria");
                }

                $dbQuery->where("$k = :pk_$k")->bindValue("pk_$k", $this->$k);
            }
        } else {
            if (!isset($this->$pk)) {
                throw new ActiveRecordException("Debe definir valor para la clave primaria");
            }

            $dbQuery->where("$pk = :pk_$pk")->bindValue("pk_$pk", $this->$pk);
        }
    }

    /**
     * Verifica si esta persistente en la BD el objeto actual
     *
     * @return boolean
     */
    public function exists()
    {
// Establece condicion de busqueda con clave primaria
        $this->wherePK(static::dbQuery());

        return $this->existsOne();
    }

    /**
     * Realiza un update del registro sobre la tabla
     *
     * @param array $data información a ser guardada
     * @return boolean
     */
    public function update($data = null)
    {
// Si es un array, se cargan los atributos en el objeto
        if (is_array($data)) {
            $this->dump($data);
        }

// Callback antes de actualizar
        if (false === $this->beforeUpdate() || false === $this->beforeSave()) {
            return false;
        }

// Callback de validaciónes
        if (false === $this->validate(true)) {
            return false;
        }

// Si no existe el registro
        if (!$this->exists()) {
            return false;
        }

// Objeto de consulta
        $dbQuery = new DbQuery($this);
// Establece condicion de busqueda con clave primaria
        $this->wherePK($dbQuery);

        $data = $this->getTableValues();
// Ejecuta la consulta con el query utilizado para el exists
        if ($statement = static::query($dbQuery->update($data))) {

            static::dispatchQueryEvent($statement, $this);

            if (Adapter::getEventDispatcher()->hasListeners(Events::UPDATE)) {
                $event = new CreateOrUpdateEvent(get_called_class(), $statement, $this);
                Adapter::getEventDispatcher()->dispatch(Events::UPDATE, $event);
            }

// Callback despues de actualizar
            $this->afterUpdate();
            $this->afterSave();
            return true;
        }

        return false;
    }

    /**
     * Elimina al objeto de la BD
     *
     * @return boolean
     */
    public function delete()
    {
// Objeto de consulta
        $dbQuery = new DbQuery($this);
// Establece condicion de busqueda con clave primaria
        $this->wherePK($dbQuery);
// Ejecuta la consulta con el query utilizado para el exists
        if ($statement = static::query($dbQuery->delete())) {
            if (Adapter::getEventDispatcher()->hasListeners(Events::DELETE)) {
                $event = new Event(get_called_class(), $statement);
                Adapter::getEventDispatcher()->dispatch(Events::DELETE, $event);
            }
            return true;
        }

        return false;
    }

    /**
     * Elimina el registro por medio de la clave primaria
     *
     * @param string $value
     * @return boolean
     */
    public static function deleteByPK($value)
    {
//creo el objeto:
        $model = static::findByPK($value);

// Ejecuta la consulta con el query utilizado para el exists
        if ($model->delete()) {
            return true;
        }

        return false;
    }

    /**
     * Elimina el registro por medio del id
     *
     * @param int $id
     * @return boolean
     */
    public static function deleteByID($id)
    {
        return static::deleteByPK((int) $id);
    }

    /**
     * Realiza una consulta SELECT y devuelve los resultados paginados.
     * @param int $page numero página a devolver
     * @param int $per_page registros por página
     * @param string $fetchMode como serán devueltos los registros (array, model, obj)
     * @return array 
     */
    public static function paginate($page, $per_page = 10, $fetchMode = null)
    {
        return Paginator::paginate(static::dbQuery(), $page, $per_page, $fetchMode);
    }

    /**
     * Ejecuta la función pasada como parametro y las consultas ejecutadas dentro de
     * dicha función se haran dentro de una transacción.
     * 
     * @example 
     * 
     * $usr = new Usuarios(array('nombre' => "Manuel", 'apellido' => "Aguirre"));
     * 
     * if($usr->transaction(function($model){
     *      $model->active = 1;
     *      return $model->save();
     * })){ 
     *      //se realizó bien 
     * }else{
     *      //error
     * }
     * 
     * En el ejemplo la función que se creó realiza el save dentro de una transacción.
     * 
     * @param \Closure $function función a ser ejecutada, la misma recibe la instancia
     * del objeto al que está haciendi la transacción.
     * @return boolean devuelve verdadero si la transacción fué exitosa.
     * @throws Exception si ocurre una excepción hace un rollback y devuelve el error.
     */
    public function transaction(\Closure $function)
    {
        self::begin();
        try {
            if (false === $function($this)) {
                self::rollback();
                return false;
            }
            self::commit();
            return true;
        } catch (\Exception $e) {
            self::rollback();
            throw $e;
        }
    }

    /**
     * Realiza un INSERT ó UPDATE dependiendo de si el objeto está ya persistido en la BD
     * @param array $data
     * @return boolean 
     */
    public function save(array $data = array())
    {
        if (count($data)) {
            $this->dump($data);
        }

        if (is_string($pk = static::metadata()->getPK()) && isset($this->$pk) && $this->exists()) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    /**
     * Inicia una transacción si es posible
     *
     * @return boolean
     */
    public static function begin()
    {
        return Adapter::factory(static::$connection)->pdo()->beginTransaction();
    }

    /**
     * Cancela una transacción si es posible
     *
     * @return boolean
     */
    public static function rollback()
    {
        return Adapter::factory(static::$connection)->pdo()->rollBack();
    }

    /**
     * Hace commit sobre una transacción si es posible
     *
     * @return boolean
     */
    public static function commit()
    {
        return Adapter::factory(static::$connection)->pdo()->commit();
    }

    /**
     * Crea una relación 1-1 inversa entre dos modelos
     *
     * @param string $relation
     *
     * model : nombre del modelo al que se refiere
     * fk : campo por el cual se relaciona (llave foránea)
     */
    protected function belongsTo($name, $model, $fk = null)
    {
        $fk || $fk = self::createTableName($model) . '_id';
        Relations::belongsTo(get_called_class(), $name, $model, $fk);
    }

    /**
     * Crea una relación 1-1 entre dos modelos
     *
     * @param string $relation
     *
     * model : nombre del modelo al que se refiere
     * fk : campo por el cual se relaciona (llave foránea)
     */
    protected function hasOne($name, $model, $fk = null)
    {
        $fk || $fk = static::table() . "_id";
        Relations::hasOne(get_called_class(), $name, $model, $fk);
    }

    /**
     * Crea una relación 1-n entre dos modelos
     *
     * @param string $relation
     *
     * model : nombre del modelo al que se refiere
     * fk : campo por el cual se relaciona (llave foránea)
     */
    protected function hasMany($name, $model, $fk = null)
    {
        $fk || $fk = static::table() . "_id";
        Relations::hasMany(get_called_class(), $name, $model, $fk);
    }

    /**
     * Crea una relación n-n o 1-n inversa entre dos modelos
     *
     * @param string $relation
     *
     * model : nombre del modelo al que se refiere
     * fk : campo por el cual se relaciona (llave foranea)
     * key: campo llave que identifica al propio modelo
     * through : através de que tabla
     */
    protected function hasAndBelongsToMany($name, $model, $through, $fk = null, $key = null)
    {
        $fk || $fk = self::createTableName($model) . '_id';
        $key || $key = static::table() . '_id';
        Relations::hasAndBelongsToMany(get_called_class(), $name, $model, $through, $fk, $key);
    }

    /**
     * Devuelve los registros del modelo al que se está asociado.
     *
     * @param string $name nombre de la relación
     * @return array|null|false si existen datos devolverá un array,
     * null si no hay datos asociados aun, y false si no existe ninguna asociación.
     */
    public function get($name, array $conditions = array())
    {
        if ($config = Relations::get(get_called_class(), $name, Relations::BELONGS_TO)) {

            $fk = $config['fk'];

            if (!isset($this->{$fk})) {
                return null;
            }

            return $model::findBy(array($fk => $this->{$fk}) + $conditions);
        }

        if ($config = Relations::get(get_called_class(), $name, Relations::HAS_ONE)) {

            $fk = $config['fk'];

            if (!isset($this->{$fk})) {
                return null;
            }

            $conditions = array($config['model']::metadata()->getPK() => $this->{$fk}) + $conditions;

            return $model::findBy($conditions);
        }

        if ($config = Relations::get(get_called_class(), $name, Relations::HAS_MANY)) {

            if (!isset($this->{static::metadata()->getPK()})) {
                return array();
            }

            $fk = $config['fk'];

            $pk = $this->{static::metadata()->getPK()};

            return $model::findAllBy(array($fk => $pk) + $conditions);
        }

        if ($config = Relations::get(get_called_class(), $name, Relations::HAS_AND_BELONGS_TO_MANY)) {

            $pk1 = static::metadata()->getPK();

            if (!isset($this->{$pk1})) {
                return array();
            }

            $fk = $config['fk'];
            $key = $config['key'];
            $pk2 = $config['model']::metadata()->getPK();
            $thisTable = static::table();
            $modelTable = $config['model']::table();
            $through = $config['through']::table();

            $query = $config['model']::createQuery()
                    ->select("$modelTable.*")
                    ->join("$through as th", "th.{$fk} = {$modelTable}.{$pk2}")
                    ->where("th.{$key} = :pk")
                    ->bindValue('pk', $this->{$pk1});

            static::createConditions($query, $conditions);

            return $config['model']::findAll();
        }
        throw new ActiveRecordException("No existe la asociacion de nombre $model en el modelo " . get_called_class());
    }

    public function __call($name, $arguments)
    {
        if (count($arguments)) {
            $arguments = current($arguments);
        } else {
            $arguments = array();
        }
        return $this->get($name, $arguments);
    }

    /**
     * Devuelve la instancia del DbQuery asociado a un modelo si existe, si no lo crea y lo devuelve.
     * @return DbQuery
     */
    protected static function dbQuery(DbQuery $query = null)
    {
        static $dbQuery = null;

        if ($query) {
            $dbQuery = $query;
        } elseif (!$dbQuery) {
            return static::createQuery();
        }

        return $dbQuery;
    }

    public function getRelationalModel($fk, $inType = 'belongsTo')
    {
        if (!isset(self::$relations[get_called_class()][$inType])) {
            return false;
        }
        if (in_array($fk, self::$relations[get_called_class()][$inType])) {
            return array_search($fk, self::$relations[get_called_class()][$inType]);
        }

        return false;
    }

    /**
     * Crea el nombre de la tabla a partir de la clase que hace de Modelo
     * @param string $className nombre de la clase.
     * @return string 
     */
    private static function createTableName($className)
    {
        $className = explode('\\', $className);
        $className = end($className);
        return strtolower(preg_replace('/(.+)([A-Z])/', "$1_$2", $className));
    }

    /**
     * Método llamado al serializar un objeto ActiveRecord
     * 
     * devuelve los atributos que serán serializados.
     * 
     * @return string 
     */
    public function __sleep()
    {
        return static::metadata()->getAttributesList();
    }

    /**
     * Revierte la serialización de un objeto y llama al constructor del mismo
     * para inicializar dicho objeto de manera correcta.
     * @param string $serialized 
     */
    public function __wakeup()
    {
        $this->__construct();
    }

    protected static function dispatchQueryEvent(PDOStatement $statement, $result)
    {
        if (Adapter::getEventDispatcher()->hasListeners(Events::QUERY)) {
            $event = new Event(get_called_class(), $statement, $result);
            Adapter::getEventDispatcher()->dispatch(Events::QUERY, $event);
        }

        return $result;
    }

}
