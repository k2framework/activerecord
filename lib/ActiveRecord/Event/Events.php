<?php

namespace ActiveRecord\Event;

/**
 * Description of KumbiaEvents
 *
 * @author manuel
 */
final class Events
{

    const BEFORE_QUERY = 'activerecord.beforequery';
    const BEFORE_UPDATE = 'activerecord.beforeupdate';
    const BEFORE_CREATE = 'activerecord.beforecreate';
    const BEFORE_DELETE = 'activerecord.beforedelete';
    const BEFORE_SELECT = 'activerecord.beforeselect';
    const AFTER_QUERY = 'activerecord.afterquery';
    const AFTER_UPDATE = 'activerecord.afterupdate';
    const AFTER_CREATE = 'activerecord.aftercreate';
    const AFTER_DELETE = 'activerecord.afterdelete';

}