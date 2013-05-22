<?php

namespace ActiveRecord\Event;

/**
 * Description of KumbiaEvents
 *
 * @author manuel
 */
final class Events
{

    const QUERY = 'activerecord.query';
    const UPDATE = 'activerecord.update';
    const CREATE = 'activerecord.create';
    const DELETE = 'activerecord.delete';

}