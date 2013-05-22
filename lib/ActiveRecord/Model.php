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
use ActiveRecord\Event\Event;
use ActiveRecord\Event\Events;
use ActiveRecord\Query\DbQuery;
use ActiveRecord\Adapter\Adapter;
use ActiveRecord\Metadata\Metadata;
use ActiveRecord\Event\SelectEvent;
use ActiveRecord\Paginator\Paginator;
use ActiveRecord\Exception\SqlException;
use ActiveRecord\Event\CreateOrUpdateEvent;
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
class Model implements \Serializable
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
     *
     * @var array
     */
    private static $relations = array();

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
        if (!isset(self::$relations[get_called_class()])) {
            self::$relations[get_called_class()] = array();
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

        if (Adapter::getEventDispatcher()->hasListeners(Events::BEFORE_SELECT)) {
            $event = new SelectEvent(get_called_class(), $dbQuery);
            Adapter::getEventDispatcher()->dispatch(Events::BEFORE_SELECT, $event);
        }

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
        // Crea la instancia de DbQuery
        return self::dbQuery(new DbQuery(get_called_class()));
    }

    /**
     * Efectua una consulta SELECT y devuelve el primer registro encontrado.
     *
     * @param string $fetchMode
     * @return Model
     */
    public static function find($fetchMode = self::FETCH_MODEL)
    {
        $dbQuery = self::dbQuery()->select();

        $result = static::query($dbQuery, $fetchMode)->fetch();

        if (Adapter::getEventDispatcher()->hasListeners(Events::AFTER_SELECT)) {
            $event = new SelectEvent(get_called_class(), $dbQuery, $result, true);
            Adapter::getEventDispatcher()->dispatch(Events::AFTER_SELECT, $event);
        }

        return $result;
    }

    /**
     * Efectua una consulta SELECT y devuelve un arreglo con los registros devueltos.
     *
     * @param string $fetchMode
     * @return array
     */
    public static function findAll($fetchMode = null)
    {
        $dbQuery = self::dbQuery()->select();

        $result = static::query($dbQuery, $fetchMode)->fetchAll();

        if (Adapter::getEventDispatcher()->hasListeners(Events::AFTER_SELECT)) {
            $event = new SelectEvent(get_called_class(), $dbQuery, $result, true);
            Adapter::getEventDispatcher()->dispatch(Events::AFTER_SELECT, $event);
        }

        return $result;
    }

    /**
     * Crea condiciones en el DbQuery a partir de un array
     * @param \ActiveRecord\Query\DbQuery $q
     * @param array $conditions
     */
    private static function createConditions(DbQuery $q, array $conditions = array())
    {
        $x = 0;
        foreach ($conditions as $column => $value) {
            if (is_array($value)) {
                $q->where("$column IN (:v$v)")
                        ->bindValue("v$x", "'" . join("','", $value) . "'");
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
        self::createConditions(static::createQuery());

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
        self::createConditions(static::createQuery());

        return static::findAll($fetchMode);
    }

    /**
     * Buscar por medio de la clave primaria
     *
     * @param string $value
     * @param string $fetchMode
     * @return Model
     */
    public static function findByPK($value, $fetchMode = null)
    {
        $pk = static::metadata()->getPK();

        $query = static::createQuery()
                ->select()
                ->where("$pk = :pk")
                ->bindValue('pk', $value);
        // Realiza la busqueda y retorna el objeto ActiveRecord
        return static::find($fetchMode);
    }

    /**
     * Buscar por medio del id
     *
     * @param string $value
     * @param int $id
     * @return Model
     */
    public static function findByID($id, $fetchMode = null)
    {
        return static::findByPK((int) $id, $fetchMode);
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

        if (Adapter::getEventDispatcher()->hasListeners(Events::BEFORE_CREATE)) {
            $event = new CreateOrUpdateEvent(get_called_class(), $data);
            Adapter::getEventDispatcher()->dispatch(Events::BEFORE_CREATE, $event);
        }

        // Ejecuta la consulta
        if (static::query($dbQuery->insert($data))) {

            // Convenio patron identidad en activerecord si PK es "id"
            if (is_string($pk = static::metadata()->getPK()) && (!isset($this->$pk) || $this->$pk == '')) {
                // Obtiene el ultimo id insertado y lo carga en el objeto
                $this->$pk = Adapter::factory(static::$connection)
                                ->pdo()->lastInsertId();
            }

            if (Adapter::getEventDispatcher()->hasListeners(Events::AFTER_CREATE)) {
                $event = new CreateOrUpdateEvent(get_called_class(), $data);
                Adapter::getEventDispatcher()->dispatch(Events::AFTER_CREATE, $event);
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
        return static::query($query)->rowCount();
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
        return static::query($query->delete())->rowCount();
    }

    /**
     * Cuenta el numero de registros devueltos en una consulta de tipo SELECT
     * @param \ActiveRecord\Query\DbQuery $query
     * @return int
     */
    public static function count(DbQuery $query = null)
    {
        self::dbQuery($query)->columns("COUNT(*) AS n");
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
        $this->wherePK(self::dbQuery());

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

        if (Adapter::getEventDispatcher()->hasListeners(Events::BEFORE_UPDATE)) {
            $event = new CreateOrUpdateEvent(get_called_class(), $data);
            Adapter::getEventDispatcher()->dispatch(Events::BEFORE_UPDATE, $event);
        }

        // Ejecuta la consulta con el query utilizado para el exists
        if (static::query($dbQuery->update($data))) {

            if (Adapter::getEventDispatcher()->hasListeners(Events::AFTER_UPDATE)) {
                $event = new CreateOrUpdateEvent(get_called_class(), $data);
                Adapter::getEventDispatcher()->dispatch(Events::AFTER_UPDATE, $event);
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

        if (Adapter::getEventDispatcher()->hasListeners(Events::BEFORE_DELETE)) {
            $event = new Event(get_called_class());
            Adapter::getEventDispatcher()->dispatch(Events::BEFORE_DELETE, $event);
        }
        // Ejecuta la consulta con el query utilizado para el exists
        if (static::query($dbQuery->delete())) {
            if (Adapter::getEventDispatcher()->hasListeners(Events::AFTER_DELETE)) {
                $event = new Event(get_called_class());
                Adapter::getEventDispatcher()->dispatch(Events::AFTER_DELETE, $event);
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
        return Paginator::paginate(self::dbQuery(), $page, $per_page, $fetchMode);
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
        $this->begin();
        try {
            if (false === $function($this)) {
                $this->rollback();
                return false;
            }
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
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
    public function begin()
    {
        return Adapter::factory(static::$connection)->pdo()->beginTransaction();
    }

    /**
     * Cancela una transacción si es posible
     *
     * @return boolean
     */
    public function rollback()
    {
        return Adapter::factory(static::$connection)->pdo()->rollBack();
    }

    /**
     * Hace commit sobre una transacción si es posible
     *
     * @return boolean
     */
    public function commit()
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
    protected function belongsTo($model, $fk = null)
    {
        $fk || $fk = self::createTableName($model) . '_id';
        self::$relations[get_called_class()]['belongsTo'][$model] = $fk;
    }

    /**
     * Crea una relación 1-1 entre dos modelos
     *
     * @param string $relation
     *
     * model : nombre del modelo al que se refiere
     * fk : campo por el cual se relaciona (llave foránea)
     */
    protected function hasOne($model, $fk = null)
    {
        $fk || $fk = static::table() . "_id";
        self::$relations[get_called_class()]['hasOne'][$model] = $fk;
    }

    /**
     * Crea una relación 1-n entre dos modelos
     *
     * @param string $relation
     *
     * model : nombre del modelo al que se refiere
     * fk : campo por el cual se relaciona (llave foránea)
     */
    protected function hasMany($model, $fk = null)
    {
        $fk || $fk = static::table() . "_id";
        self::$relations[get_called_class()]['hasMany'][$model] = $fk;
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
    protected function hasAndBelongsToMany($model, $through, $fk = null, $key = null)
    {
        $fk || $fk = self::createTableName($model) . '_id';
        $key || $key = static::table() . '_id';
        self::$relations[get_called_class()]['hasAndBelongsToMany']
                [$model] = compact('through', 'fk', 'key');
    }

    /**
     * Devuelve los registros del modelo al que se está asociado.
     *
     * @param string $model nombre del modelo asociado
     * @return array|null|false si existen datos devolverá un array,
     * null si no hay datos asociados aun, y false si no existe ninguna asociación.
     */
    public function get($model)
    {
        if (!isset(self::$relations[get_called_class()])) {
            return false;
        }

        if (isset(self::$relations[get_called_class()]['belongsTo']) &&
                isset(self::$relations[get_called_class()]['belongsTo'][$model])) {

            if (!isset($this->{$fk})) {
                return false;
            }

            $fk = self::$relations[get_called_class()]['belongsTo'][$model];

            return $model::findBy(array($fk => $this->{$fk}));
        }

        if (isset(self::$relations[get_called_class()]['hasOne']) &&
                isset(self::$relations[get_called_class()]['hasOne'][$model])) {

            if (!isset($this->{$fk})) {
                return false;
            }

            $fk = self::$relations[get_called_class()]['hasOne'][$model];

            return $model::findBy(array($model::metadata()->getPK() => $this->{$fk}));
        }

        if (isset(self::$relations[get_called_class()]['hasMany']) &&
                isset(self::$relations[get_called_class()]['hasMany'][$model])) {

            if (!isset($this->{static::metadata()->getPK()})) {
                return array();
            }

            $fk = self::$relations[get_called_class()]['hasMany'][$model];

            $pk = $this->{static::metadata()->getPK()};
            return $model::findAllBy(array($fk => $pk));
        }

        if (isset(self::$relations[get_called_class()]['hasAndBelongsToMany']) &&
                isset(self::$relations[get_called_class()]['hasAndBelongsToMany'][$model])) {

            $pk1 = static::metadata()->getPK();

            if (!isset($this->{$pk1})) {
                return array();
            }

            $relation = self::$relations[get_called_class()]['hasAndBelongsToMany'][$model];

            $fk = $relation['fk'];
            $key = $relation['key'];
            $pk2 = $model::metadata()->getPK();
            $thisTable = static::table();
            $modelTable = $model::table();
            $through = $relation['through']::table();

            $model::createQuery()
                    ->select("$modelTable.*")
                    ->join("$through as th", "th.{$fk} = {$modelTable}.{$pk2}")
                    //->join("$thisTable as this", "this.{$pk1} = th.{$key}")
                    ->where("th.{$key} = :pk")
                    //->where("this.{$pk1} = :pk")
                    ->bindValue('pk', $this->{$pk1});

            return $model::findAll();
        }
        throw new ActiveRecordException("No existe la asociacion con $model en el modelo " . get_called_class());
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
     * serializa solo la data que representa los campos de la tabla.
     * 
     * @return string 
     */
    public function serialize()
    {
        $data = array_intersect_key(get_object_vars($this), static::metadata()->getAttributes());
        return serialize($data);
    }

    /**
     * Revierte la serialización de un objeto y llama al constructor del mismo
     * para inicializar dicho objeto de manera correcta.
     * @param string $serialized 
     */
    public function unserialize($serialized)
    {
        $this->__construct(unserialize($serialized));
    }

}
