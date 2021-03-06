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
use NakedPhp\MetaModel\NakedObject;

/**
 * This class act as a small container for entity instances which the user is working on.
 * Its goal is to be kept in a php session for working on the objects contained.
 * Then the result can be saved by a DataMapper.
 */
class BareContainer implements EntityContainer
{
    /**
     * @var array   NakedObject instances
     */
    private $_objects = array();

    private $_stateDiscoverer;

    private $_states = array();
    private $_counter = 0;

    public function __construct(StateDiscoverer $stateDiscoverer = null)
    {
        $this->_stateDiscoverer = $stateDiscoverer;
    }

    public function __sleep()
    {
        return array('_objects', '_states', '_counter');
    }

    public function initStateDiscoverer($stateDiscoverer)
    {
        $this->_stateDiscoverer = $stateDiscoverer;
    }

    /**
     * {inheritdoc}
     */
    public function add(NakedObject $object, $state = null)
    {
        $index = $this->contains($object);
        if ($index) {
            return $index;
        }
        $this->_counter++;
        $this->_objects[$this->_counter] = $object;
        if ($state === null) {
            $state = $this->_stateDiscoverer->isTransient($object) 
                   ? self::STATE_NEW
                   : self::STATE_DETACHED;
        }
        $this->_states[$this->_counter] = $state;
        return $this->_counter;
    }

    /**
     * {inheritdoc}
     */
    public function delete($key)
    {
        unset($this->_objects[$key]);
        unset($this->_states[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->_objects = array();
        $this->_states  = array();
        $this->_counter = 0;
    }

    /**
     * {inheritdoc}
     */
    public function replace($key, NakedObject $object)
    {
        $this->_objects[$key] = $object;
    }

    /**
     * {inheritdoc}
     */
    public function get($key)
    {
        return $this->_objects[$key];
    }

    /**
     * {inheritdoc}
     */
    public function setState($key, $state)
    {
        $this->_states[$key] = $state;
    }

    /**
     * {inheritdoc}
     */
    public function getState($key)
    {
        return $this->_states[$key];
    }

    /**
     * {inheritdoc}
     */
    public function contains(NakedObject $object)
    {
        foreach ($this->_objects as $index => $current) {
            if ($object->equals($current)) {
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
