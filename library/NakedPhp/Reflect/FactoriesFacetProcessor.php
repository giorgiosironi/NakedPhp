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
 * @package    NakedPhp_Reflect
 */

namespace NakedPhp\Reflect;
use NakedPhp\MetaModel\AssociationIdentifyingFacetFactory;
use NakedPhp\MetaModel\FacetHolder;
use NakedPhp\Reflect\MethodRemover;

/**
 * Hides a high number of FacetFactory instances beyond its interface.
 */
class FactoriesFacetProcessor
{
    protected $_facetFactories;

    public function __construct(array $factories = array())
    {
        $this->_facetFactories = $factories;
    }

    /**
     * Merges the results of @see AssociationIdentifyingFacetFactory::removePropertyAccessors() calls.
     * @return array    ReflectionMethod instances
     */
    public function removePropertyAccessors(MethodRemover $remover)
    {
        $methods = array();
        foreach ($this->_getAssociationIdentifyingFacetFactories() as $factory) {
            $methods += $factory->removePropertyAccessors($remover);
        }
        return $methods;
    }

    /**
     * @return array    AssociationIdentifyingFacetFactory instances
     */
    protected function _getAssociationIdentifyingFacetFactories()
    {
        $factories = array();
        foreach ($this->_facetFactories as $factory) {
            if ($factory instanceof AssociationIdentifyingFacetFactory) {
                $factories[] = $factory;
            }
        }
        return $factories;
    }

    public function processClass(\ReflectionClass $class, MethodRemover $remover, FacetHolder $holder)
    {
        foreach ($this->_facetFactories as $factory) {
            $factory->processClass($class, $remover, $holder);
        }
    }

    public function processMethod(\ReflectionClass $class, \ReflectionMethod $method, MethodRemover $remover, FacetHolder $holder)
    {
        foreach ($this->_facetFactories as $factory) {
            $factory->processMethod($class, $method, $remover, $holder);
        }
    }
}
