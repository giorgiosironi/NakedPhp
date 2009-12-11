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

namespace NakedPhp\Mvc\EntityContainer;
use NakedPhp\Mvc\EntityContainer;

/**
 * This class act as a small container for entity instances which the user is working on.
 * Its goal is to be kept in a php session for working on the objects contained.
 * Then the result can be saved by a DataMapper.
 */
class UnwrappedContainer implements EntityContainer
{
    private $_objects = array();
    private $_states = array();
    private $_counter = 0;

    /**
     * @param object $object   object to be added idempotently
     * @param integer $state   one of the STATE_* constants
     * @return integer         the key of the object in this container
     */
    public function add($object, $state = self::STATE_NEW)
    {
        $index = $this->contains($object);
        if ($index) {
            return $index;
        }
        $this->_counter++;
        $this->_objects[$this->_counter] = $object;
        $this->_states[$this->_counter] = $state;
        return $this->_counter;
    }

    /**
     * @param integer $key      key returned during insertion
     */
    public function delete($key)
    {
        unset($this->_objects[$key]);
        unset($this->_states[$key]);
    }

    /**
     * @param integer $key      key returned during insertion
     * @param object $object   object to be added idempotently
     */
    public function replace($key, $object)
    {
        $this->_objects[$key] = $object;
    }

    /**
     * @param integer $key  key for the object
     * @return object
     */
    public function get($key)
    {
        return $this->_objects[$key];
    }

    /**
     * @param integer $key    key for the object
     * @param integer $state  one of the STATE_* constants
     */
    public function setState($key, $state)
    {
        $this->_states[$key] = $state;
    }

    /**
     * @param integer $key  key for the object
     * @return one of the STATE_* constants
     */
    public function getState($key)
    {
        return $this->_states[$key];
    }

    /**
     * @param object        $object
     * @return integer      the object key; false if it's not contained
     */
    public function contains($object)
    {
        foreach ($this->_objects as $index => $current) {
            if ($object === $current) {
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
