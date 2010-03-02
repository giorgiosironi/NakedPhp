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
use NakedPhp\MetaModel\MethodFilteringFacetFactory;
use NakedPhp\Reflect\MethodRemover;

/**
 * Hides a high number of FacetFactory instances beyond its interface.
 * The operation implemented are the same of a normal FacetFactory,
 * but are repeated on all the factories.
 */
class FactoriesFacetProcessor implements FacetProcessor
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
     * @return bool  true if at least one @see MethodFilteringFacetFactory recognizes $method.
     */
    public function recognizes(\ReflectionMethod $method)
    {
        foreach ($this->_getMethodFilteringFacetFactories() as $factory) {
            if ($factory->recognizes($method)) {
                return true;
            }
        }
        return false;
    }

    public function processClass(\ReflectionClass $class, MethodRemover $remover, FacetHolder $holder, $featureType = null)
    {
        $factories = $this->_filterFacetFactoriesByFeatureType($featureType);
        foreach ($factories as $i => $factory) {
            $factory->processClass($class, $remover, $holder);
        }
    }

    public function processMethod(\ReflectionClass $class, \ReflectionMethod $method, MethodRemover $remover, FacetHolder $holder, $featureType = null)
    {
        $factories = $this->_filterFacetFactoriesByFeatureType($featureType);
        foreach ($factories as $i => $factory) {
            $factory->processMethod($class, $method, $remover, $holder);
        }
    }

    /**
     * @return array    AssociationIdentifyingFacetFactory instances
     */
    protected function _getAssociationIdentifyingFacetFactories()
    {
        return $this->_filterFacetFactories('NakedPhp\MetaModel\AssociationIdentifyingFacetFactory');
    }
    /**
     * @return array    MethodFilteringFacetFactory instances
     */
    protected function _getMethodFilteringFacetFactories()
    {
        return $this->_filterFacetFactories('NakedPhp\MetaModel\MethodFilteringFacetFactory');
    }

    /**
     * @param string $interface fully qualified interface name
     * @return array    $interface instances
     */
    protected function _filterFacetFactories($interface)
    {
        $factories = array();
        foreach ($this->_facetFactories as $factory) {
            if ($factory instanceof $interface) {
                $factories[] = $factory;
            }
        }
        return $factories;
    }

    /**
     * @param string $requestedType     a NakedObjectFeatureType constant
     * @return array
     */
    protected function _filterFacetFactoriesByFeatureType($requestedType)
    {
        if ($requestedType === null) {
            return $this->_facetFactories;
        }
        $filteredFactories = array();
        foreach ($this->_facetFactories as $factory) {
            $supportedTypes = $factory->getFeatureTypes();
            if (in_array($requestedType, $supportedTypes)) {
                $filteredFactories[] = $factory;
            }
        }
        return $filteredFactories;
    }
}
