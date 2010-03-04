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
 * @package    NakedPhp_ProgModel
 */

namespace NakedPhp\ProgModel;
use NakedPhp\MetaModel\NakedObjectSpecification;

/**
 * Wraps an entity object.
 */
class NakedBareObject extends AbstractNakedObject implements \IteratorAggregate
{
    /**
     * POPO to wrap.
     */
    protected $_wrapped;

    /**
     * @param object $wrapped   domain object to wrap.
     */
    public function __construct($wrapped = null, NakedObjectSpecification $class = null)
    {
        $this->_wrapped = $wrapped;
        $this->_class   = $class;
    }

    /**
     * @return NakedObjectSpecification
     */
    public function getSpecification()
    {
        return $this->_class;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        $state = array();
        foreach ($this->_class->getAssociations() as $name => $field) {
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
            $field = $this->_class->getAssociation($fieldName);
            $field->setAssociation($this, $value);
        }
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->getState());
    }

    /**
     * @param object $object
     * @return bool  true if $object is the same domain object wrapped
     */
    public function isWrapping($object)
    {
        return $this->_wrapped === $object;
    }

    /**
     * {@inheritdoc}
     */
    public function getObject()
    {
        return $this->_wrapped;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if (method_exists($this->_wrapped, '__toString')) {
            $result = (string) $this->_wrapped;
        } else if (($collFacet = $this->_class->getFacet('Collection')) !== null) {
            $typeOfFacet = $this->_class->getFacet('Collection\TypeOf');
            $result = count($this->_wrapped) . ' ' . (string) $typeOfFacet->valueSpec();
        } else {
            $result = 'OBJECT';
        }
        return $result;
    }

    /**
     * implements decoration of wrapped object
     */
    public function __call($name, array $args = array())
    {
        if (method_exists($this->_wrapped, $name)) {
            return call_user_func_array(array($this->_wrapped, $name), $args);
        }
        throw new Exception("Method $name does not exist.");
    }

    /**
     * {@inheritdoc}
     */
    public function createNewInstance($object, NakedObjectSpecification $spec)
    {
        return new self($object, $spec);
    }
}
