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
 * @package    NakedPhp_Stubs
 */

namespace NakedPhp\Stubs;
use NakedPhp\Metadata\NakedBareEntity;
use NakedPhp\Metadata\NakedObjectAction;
use NakedPhp\Metadata\OneToOneAssociation;
use NakedPhp\Metadata\Facet;

class NakedEntityStub extends NakedBareEntity
{
    protected $_className;
    protected $_state;
    protected $_fields = array();

    public function getClassName()
    {
        return $this->_className;
    }

    public function setClassName($className)
    {
        $this->_className = $className;
    }

    public function setState(array $state)
    {
        $this->_state = $state;
    }

    public function getState()
    {
        return $this->_state;
    }
    
    public function setField($name, OneToOneAssociation $field)
    {
        return $this->_fields[$name] = $field;
    }

    public function getField($name)
    {
        return $this->_fields[$name];
    }

    public function addFacet(Facet $facet)
    {
        $type = $facet->facetType();
        $this->_facets[$type] = $facet;
    }

    public function getFacet($type)
    {
        if (isset($this->_facets[$type])) {
            return $this->_facets[$type];
        }
        return null;
    }
}
