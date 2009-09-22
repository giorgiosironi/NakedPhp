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
 * @package    NakedPhp_Service
 */

namespace NakedPhp\Service;
use NakedPhp\Metadata\NakedEntity;

/**
 * This class act as a small container for NakedEntity instances.
 * Its goal is to be kept in a php session for working on the objects contained.
 * Then the result can be saved by a DataMapper.
 */
class EntityContainer implements \IteratorAggregate
{
    private $_objects = array();
    private $_counter = 0;

    public function __construct()
    {
    }

    /**
     * @param NakedEntity $object   object to be added idempotently
     * @return integer  the key of the object in this container
     */
    public function add(NakedEntity $object)
    {
        $index = $this->contains($object);
        if ($index) {
            return $index;
        }
        $this->_counter++;
        $this->_objects[$this->_counter] = $object;
        return $this->_counter;
    }

    /**
     * @param integer $key  key for the object
     * @return NakedEntity
     */
    public function get($key)
    {
        return $this->_objects[$key];
    }

    /**
     * @param NakedEntity $object
     * @return integer      the object key; false if it's not contained
     */
    public function contains(NakedEntity $object)
    {
        foreach ($this->_objects as $index => $o) {
            if ($o->equals($object)) {
                return $index;
            }
        }
        return false;
    }

    /**
     * implements the IteratorAggregate interface
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_objects);
    }
}
