<?php

namespace ActiveRecord;

class Relations
{

    const BELONGS_TO = 1;
    const HAS_ONE = 2;
    const HAS_MANY = 3;
    const HAS_AND_BELONGS_TO_MANY = 4;

    protected static $definitions = array();

    public static function add($class, $type, $name, array $config)
    {
        static::$definitions[$class][$name][$type] = $config;
    }

    public static function has($class, $name = null, $type = null)
    {
        if ($type) {
            return isset(static::$definitions[$class][$name][$type]);
        } elseif ($name) {
            return isset(static::$definitions[$class][$name]);
        } else {
            return isset(static::$definitions[$class]);
        }
    }

    public static function get($class, $name, $type = null)
    {
        if (static::has($class, $name)) {
            if ($type) {
                return static::has($class, $name, $type) ?
                        static::$definitions[$class][$name][$type] : null;
            } else {
                return static::$definitions[$class][$name];
            }
        } else {
            return null;
        }
    }

    public static function belongsTo($class, $name, $model, $fk)
    {
        static::add($class, self::BELONGS_TO, $name, compact('model', 'fk'));
    }

    public static function hasOne($class, $name, $model, $fk)
    {
        static::add($class, self::HAS_ONE, $name, compact('model', 'fk'));
    }

    public static function hasMany($class, $name, $model, $fk)
    {
        static::add($class, self::HAS_MANY, $name, compact('model', 'fk'));
    }

    public static function hasAndBelongsToMany($class, $name, $model, $through, $fk, $key)
    {
        static::add($class, self::HAS_AND_BELONGS_TO_MANY
                , $name, compact('model', 'through', 'fk', 'key'));
    }

    public static function setLoaded($class)
    {
        static::$definitions[$class] = array();
    }

}
