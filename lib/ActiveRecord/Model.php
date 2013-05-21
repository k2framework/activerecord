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
    protected $connection = null;

    /**
     * Tabla origen de datos
     *
     * @var string
     */
    protected $table = null;

    /**
     * Esquema de datos
     *
     * @var string
     */
    protected $schema = null;

    /**
     * Objeto DbQuery para implementar chain
     *
     * @var Obj
     */
    private static $dbQuery = null;

    /**
     * ResulSet PDOStatement
     *
     * @var \PDOStatement
     */
    protected $statemet = null;

    /**
     * Modo de obtener datos
     *
     * @var integer
     */
    protected $fetchMode = self::FETCH_MODEL;

    /**
     * Instancias de metadata de modelos
     *
     * @var array
     */
    private static $metadata = array();

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
    public function metadata()
    {
        $model = get_called_class();

        if (!isset(self::$metadata[$model])) {
            self::$metadata[$model] = Adapter::factory($this->getConnection())
                    ->describe($this->getTable(), $this->getSchema());
        }

        return self::$metadata[$model];
    }

    /**
     * Carga el array como atributos del objeto
     *
     * @param array $data
     */
    public function dump($data)
    {
        $validFields = $this->metadata()->getAttributesList();
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
     * Indica/Obtiene el fetchModel a usar para una consulta.
     * Si se pasa el parametro para el fetchMode, se establece en el modelo.
     * si no se pasa nada ó null, se obtiene el fetchModel actual.
     * @param string $fetchMode modalidad de devolución de los registros
     * en una consulta, los parametros posibles son:
     * array: devuelve una matriz con los datos de la consulta
     * obj: devuelve un arreglo con objetos de tipo stdClass
     * model: devuelve un arreglo con objetos del tipo de la clase que hace el find.
     */
    protected function fetchMode($fetchMode = null)
    {
        // Si no se especifica toma el por defecto
        if (!$fetchMode) {
            $fetchMode = $this->fetchMode;
        } else {
            //si es especifica lo establecemos
            $this->fetchMode = $fetchMode;
        }

        if ($this->statemet instanceof \PDOStatement) {
            switch ($fetchMode) {
                // Obtener arrays
                case static::FETCH_ARRAY:
                    $this->statemet->setFetchMode(PDO::FETCH_ASSOC);
                    break;

                // Obtener instancias de objetos simples
                case static::FETCH_OBJ:
                    $this->statemet->setFetchMode(PDO::FETCH_OBJ);
                    break;

                // Obtener instancias del mismo modelo
                case static::FETCH_MODEL:
                default:
                    // Instancias de un nuevo modelo, por lo tanto libre de los atributos de la instancia actual
                    $this->statemet->setFetchMode(PDO::FETCH_CLASS, get_called_class());
            }
        }
    }

    /**
     * Asigna el nombre de la tabla que el modelo está representando.
     *
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * Obtiene el nombre de la tabla que el modelo está representando.
     *
     * @return string
     */
    public function getTable()
    {
        if (!isset($this->table)) {
            $this->table = $this->createTableName(get_class($this));
        }
        return $this->table;
    }

    /**
     * Asigna el esquema para la tabla
     *
     * @param string $schema
     * @return ActiveRecord
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
        return $this;
    }

    /**
     * Obtiene el esquema
     *
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Asigna la el nombre de la conexión a usar por el modelo.
     * 
     * El nombre debe ser el identificador de alguna de las configuraciones
     * de la clase ActiveRecord\Config\Config
     *
     * @param string $conn
     * @return ActiveRecord
     */
    public function setConnection($conn)
    {
        $this->connection = $conn;
        return $this;
    }

    /**
     * Obtiene el nombre de la conexión utilizada por el modelo
     *
     * @return string
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Ejecuta una setencia SQL aplicando Prepared Statement
     *
     * @param string $sql Setencia SQL
     * @param array $params parámetros que seran enlazados al SQL
     * @param string $fetchMode
     * @return \PDOStatement
     */
    public function sql($sql, $params = null, $fetchMode = null)
    {
        try {
            // Obtiene una instancia del adaptador y prepara la consulta
            $this->statemet = Adapter::factory($this->connection)
                    ->prepare($sql);

            // Indica el modo de obtener los datos en el ResultSet
            $this->fetchMode($fetchMode);

            // Ejecuta la consulta
            $this->statemet->execute($params);
            return $this->statemet;
        } catch (\PDOException $e) {
            if ($this->statemet instanceof \PDOStatement) {
                throw new SqlException($e, $this->statemet);
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
    public function query($dbQuery, $fetchMode = null)
    {
        $dbQuery->table($this->getTable());

        static::createQuery();

        // Asigna el esquema si existe
        if ($this->schema) {
            $dbQuery->schema($this->schema);
        }

        try {
            // Obtiene una instancia del adaptador y prepara la consulta
            $this->statemet = Adapter::factory($this->connection)
                    ->prepareDbQuery($dbQuery);

            // Indica el modo de obtener los datos en el ResultSet
            $this->fetchMode($fetchMode);

            // Ejecuta la consulta
            $this->statemet->execute($dbQuery->getBind());
            return $this->statemet;
        } catch (\PDOException $e) {
            if ($this->statemet instanceof \PDOStatement) {
                throw new SqlException($e, $this->statemet, $dbQuery->getBind());
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
        return self::dbQuery(new DbQuery(new static()));
    }

    /**
     * Efectua una consulta SELECT y devuelve el primer registro encontrado.
     *
     * @param string $fetchMode
     * @return Model
     */
    public static function find($fetchMode = null)
    {
        $model = self::dbQuery()->getModel();

        $dbQuery = self::dbQuery()->select();

        if (Adapter::getEventDispatcher()->hasListeners(Events::BEFORE_SELECT)) {
            $event = new SelectEvent($model, $dbQuery);
            Adapter::getEventDispatcher()->dispatch(Events::BEFORE_SELECT, $event);
        }

        $result = $model->query($dbQuery, $fetchMode)->fetch();

        if (Adapter::getEventDispatcher()->hasListeners(Events::AFTER_SELECT)) {
            $event = new SelectEvent($model, $dbQuery, $result, true);
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
        $model = new static();

        $dbQuery = self::dbQuery()->select();

        if (Adapter::getEventDispatcher()->hasListeners(Events::BEFORE_SELECT)) {
            $event = new SelectEvent($model, $dbQuery);
            Adapter::getEventDispatcher()->dispatch(Events::BEFORE_SELECT, $event);
        }

        $result = $model->query($dbQuery, $fetchMode)->fetchAll();

        if (Adapter::getEventDispatcher()->hasListeners(Events::AFTER_SELECT)) {
            $event = new SelectEvent($model, $dbQuery, $result, true);
            Adapter::getEventDispatcher()->dispatch(Events::AFTER_SELECT, $event);
        }

        return $result;
    }

    /**
     * Busca por medio de una columna especifica
     *
     * @param string $column columna de busqueda
     * @param string $value valor para la busqueda
     * @param string $fetchMode
     * @return ActiveRecord
     */
    public static function findBy($column, $value, $fetchMode = null)
    {
        static::createQuery()
                ->where("$column = :value")
                ->bindValue('value', $value);
        return static::find($fetchMode);
    }

    /**
     * Busca por medio de una columna especifica y obtiene todas la coincidencias
     *
     * @param string $column columna de busqueda
     * @param string $value valor para la busqueda
     * @param string $fetchMode
     * @return ActiveRecord
     */
    public static function findAllBy($column, $value, $fetchMode = null)
    {
        if (is_array($value)) {
            $query = static::createQuery();
            $in = array();
            foreach ($value as $k => $v) {
                $in[] = ":in_$k";
                $query->bindValue("in_$k", $v);
            }
            $query->where("$column IN (" . join(',', $in) . ")");
        } else {
            static::createQuery()
                    ->where("$column = :value")
                    ->bindValue('value', $value);
        }
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
        $model = new static();

        $pk = $model->metadata()->getPK();

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
        foreach ($this->metadata()->getAttributes() as $fieldName => $attr) {

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
        $dbQuery = new DbQuery($this);

        $data = $this->getTableValues();

        if (isset($data[$this->metadata()->getPK()])) {
            unset($data[$this->metadata()->getPK()]);
        }

        if (Adapter::getEventDispatcher()->hasListeners(Events::BEFORE_CREATE)) {
            $event = new CreateOrUpdateEvent($this, $data);
            Adapter::getEventDispatcher()->dispatch(Events::BEFORE_CREATE, $event);
        }

        // Ejecuta la consulta
        if ($this->query($dbQuery->insert($data))) {

            // Convenio patron identidad en activerecord si PK es "id"
            if (is_string($pk = $this->metadata()->getPK()) && (!isset($this->$pk) || $this->$pk == '')) {
                // Obtiene el ultimo id insertado y lo carga en el objeto
                $this->$pk = Adapter::factory($this->connection)
                                ->pdo()->lastInsertId();
            }

            if (Adapter::getEventDispatcher()->hasListeners(Events::AFTER_CREATE)) {
                $event = new CreateOrUpdateEvent($this, $data);
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
        $model = new static();

        // TODO: se debe verificar que el query creado es para actualizar.
        // Ejecuta la consulta
        return $model->query($query)->rowCount();
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
        $model = new static();
        // Ejecuta la consulta
        return $model->query($query->delete())->rowCount();
    }

    /**
     * Cuenta el numero de registros devueltos en una consulta de tipo SELECT
     *
     * @param string $column
     * @return integer
     */
    public static function count()
    {
        self::dbQuery()->columns("COUNT(*) AS n");
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
        $pk = $this->metadata()->getPK();

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
            $event = new CreateOrUpdateEvent($this, $data);
            Adapter::getEventDispatcher()->dispatch(Events::BEFORE_UPDATE, $event);
        }

        // Ejecuta la consulta con el query utilizado para el exists
        if ($this->query($dbQuery->update($data))) {

            if (Adapter::getEventDispatcher()->hasListeners(Events::AFTER_UPDATE)) {
                $event = new CreateOrUpdateEvent($this, $data);
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
            $event = new Event($this);
            Adapter::getEventDispatcher()->dispatch(Events::BEFORE_DELETE, $event);
        }
        // Ejecuta la consulta con el query utilizado para el exists
        if ($this->query($dbQuery->delete())) {
            if (Adapter::getEventDispatcher()->hasListeners(Events::AFTER_DELETE)) {
                $event = new Event($this);
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
        $model = new static();

        $model->fetchMode($fetchMode);

        return Paginator::paginate($model, self::dbQuery(), $page, $per_page);
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

        if (is_string($pk = $this->metadata()->getPK()) && isset($this->$pk) && $this->exists()) {
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
        return Adapter::factory($this->connection)->pdo()->beginTransaction();
    }

    /**
     * Cancela una transacción si es posible
     *
     * @return boolean
     */
    public function rollback()
    {
        return Adapter::factory($this->connection)->pdo()->rollBack();
    }

    /**
     * Hace commit sobre una transacción si es posible
     *
     * @return boolean
     */
    public function commit()
    {
        return Adapter::factory($this->connection)->pdo()->commit();
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
        $fk || $fk = $this->createTableName($model) . '_id';
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
        $fk || $fk = $this->getTable() . "_id";
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
        $fk || $fk = $this->getTable() . "_id";
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
        $fk || $fk = $this->createTableName($model) . '_id';
        $key || $key = $this->getTable() . '_id';
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

            return $model::findBy($fk, $this->{$fk});
        }

        if (isset(self::$relations[get_called_class()]['hasOne']) &&
                isset(self::$relations[get_called_class()]['hasOne'][$model])) {

            if (!isset($this->{$fk})) {
                return false;
            }

            $fk = self::$relations[get_called_class()]['hasOne'][$model];

            return $model::findBy(self::$metadata[$model]->getPK(), $this->{$fk});
        }

        if (isset(self::$relations[get_called_class()]['hasMany']) &&
                isset(self::$relations[get_called_class()]['hasMany'][$model])) {

            if (!isset($this->{$this->metadata()->getPK()})) {
                return array();
            }

            $fk = self::$relations[get_called_class()]['hasMany'][$model];

            return $model::findAllBy($fk, $this->{$this->metadata()->getPK()});
        }

        if (isset(self::$relations[get_called_class()]['hasAndBelongsToMany']) &&
                isset(self::$relations[get_called_class()]['hasAndBelongsToMany'][$model])) {

            $pk1 = $this->metadata()->getPK();

            if (!isset($this->{$pk1})) {
                return array();
            }

            $relation = self::$relations[get_called_class()]['hasAndBelongsToMany'][$model];

            $instance = new $model();

            $fk = $relation['fk'];
            $key = $relation['key'];
            $pk2 = $instance->metadata()->getPK();
            $thisTable = $this->getTable();
            $modelTable = $this->createTableName($model);
            $through = $this->createTableName($relation['through']);

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
    private static function dbQuery(DbQuery $query = null)
    {
        static $dbQuery = null;

        if (null !== $query) {
            $dbQuery = $query;
        }

        if (!$dbQuery) {
            $dbQuery = new DbQuery(new static());
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
    private function createTableName($className)
    {
        $className = basename(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $className));
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
        $data = array_intersect_key(get_object_vars($this), $this->metadata()->getAttributes());
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
