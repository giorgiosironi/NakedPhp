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
class NakedEntity extends NakedObject
{
    protected $_class;

    public function __construct($entity, NakedEntityClass $class = null)
    {
        parent::__construct($entity);
        $this->_class = $class;
    }

    /**
     * @return NakedEntityClass
     */
    public function getClass()
    {
        return $this->_class;
    }

    public function getState()
    {
        $state = array();
        foreach ($this->_class->getFields() as $name => $field) {
            $getter = 'get' . ucfirst($name);
            $state[$name] = $this->_wrapped->$getter();
        }
        return $state;
    }
}
