<?php

namespace ActiveRecord;

use \PDOStatement as Base;
use ActiveRecord\Event\Events;
use ActiveRecord\Adapter\Adapter;
use ActiveRecord\Event\AfterQueryEvent;
use ActiveRecord\Event\BeforeQueryEvent;

/**
 * Description of PDOStatement
 *
 * @author manuel
 */
class PDOStatement extends Base
{

    protected $result;

    public function execute($input_parameters = null)
    {
        //despachamos los eventos que están escuchando
        if (Adapter::getEventDispatcher()->hasListeners(Events::BEFORE_QUERY)) {
            //creamos el evento before_query
            $event = new BeforeQueryEvent($this->queryString, (array) $input_parameters);
            Adapter::getEventDispatcher()->dispatch(Events::BEFORE_QUERY, $event);
            $input_parameters = $event->getParameters();
        }

        $this->result = parent::execute($input_parameters);

        //creamos el evento after_query
        //despachamos los eventos que están escuchando
        if (Adapter::getEventDispatcher()->hasListeners(Events::AFTER_QUERY)) {
            $event = new AfterQueryEvent($this);
            Adapter::getEventDispatcher()->dispatch(Events::AFTER_QUERY, $event);
        }

        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }

}