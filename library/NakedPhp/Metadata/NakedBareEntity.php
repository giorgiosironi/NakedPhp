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
 * @package    NakedPhp_Metadata
 */

namespace NakedPhp\Metadata;

/**
 * Wraps an entity object.
 */
class NakedBareEntity extends AbstractNakedObject implements NakedEntity
{
    public function __construct($entity = null, NakedEntityClass $class = null)
    {
        parent::__construct($entity, $class);
    }

    /**
     * @return NakedEntityClass
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        $state = array();
        foreach ($this->_class->getFields() as $name => $field) {
            $getter = 'get' . ucfirst($name);
            $state[$name] = $this->_wrapped->$getter();
        }
        return $state;
    }

    /**
     * {@inheritdoc}
     */
    public function setState(array $data)
    {
        foreach ($data as $fieldName => $value) {
            if ($value instanceof self) {
                $value = $value->_wrapped;
            }
            $setter = 'set' . ucfirst($fieldName);
            $this->_wrapped->$setter($value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getField($name)
    {
        return $this->_class->getField($name);
    }

    public function hasHiddenMethod($methodName)
    {
        return $this->_class->hasHiddenMethod($methodName);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->getState());
    }
}
