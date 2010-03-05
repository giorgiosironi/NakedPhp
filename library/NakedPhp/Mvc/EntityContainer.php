<?php
/**
 * Naked Php is a framework that implements the Naked Objects pattern.
 * @copyright Copyright (C) 2009  Giorgio Sironi
 * @license http://www.gnu.org/licenses/lgpl-2.1.txt 
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * @category   NakedPhp
 * @package    NakedPhp_Mvc
 */

namespace NakedPhp\Mvc;
use NakedPhp\MetaModel\NakedObject;

interface EntityContainer extends \IteratorAggregate
{
    /**
     * Entity which has been created but is not known to Doctrine.
     */
    const STATE_NEW = 'new';

    /**
     * Entity known to Doctrine but detached from any ORM service.
     * There are no entities known to Doctrine and not detached in this container.
     */
    const STATE_DETACHED = 'detached';

    /**
     * Entity which deletion is pending.
     */
    const STATE_REMOVED = 'removed';

    /**
     * @param object $object   object to be added idempotently
     * @param integer $state   one of the STATE_* constants
     * @return integer         the key of the object in this container
     */
    public function add(NakedObject $object, $state = self::STATE_NEW);

    /**
     * @param integer $key      key returned during insertion
     */
    public function delete($key);

    /**
     * Deletes all references to entities and leaves the container empty.
     */
    public function clear();

    /**
     * @param integer $key      key returned during insertion
     * @param object $object   object to be added idempotently
     */
    public function replace($key, NakedObject $object);

    /**
     * @param integer $key  key for the object
     * @return object
     */
    public function get($key);

    /**
     * @param integer $key    key for the object
     * @param integer $state  one of the STATE_* constants
     */
    public function setState($key, $state);

    /**
     * @param integer $key  key for the object
     * @return one of the STATE_* constants
     */
    public function getState($key);

    /**
     * @param object        $object
     * @return integer      the object key; false if it's not contained
     */
    public function contains(NakedObject $object);
}
