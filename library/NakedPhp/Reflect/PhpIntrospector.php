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
use NakedPhp\MetaModel\FacetHolder;
use NakedPhp\ProgModel\PhpSpecification;
use NakedPhp\ProgModel\OneToOneAssociation;

/**
 * TODO: how to obtain Actions?
 */
class PhpIntrospector
{
    protected $_specification;
    protected $_facetProcessor;
    protected $_reflectionClass;
    protected $_methodRemover;

    public function __construct(PhpSpecification $specification,
                                FacetProcessor $facetProcessor)
    {
        $this->_specification = $specification;
        $this->_facetProcessor = $facetProcessor;
        // FIX: real work; probably necessary since all methods need reflection objects to work
        $this->_reflectionClass = new \ReflectionClass($this->_specification->getClassName());
        $this->_methods = new \ArrayObject($this->_reflectionClass->getMethods());
        $this->_methodRemover = new ArrayObjectMethodRemover($this->_methods);
    }

    public function introspectClass()
    {
        $this->_processClass($this->_specification);

        foreach ($this->_methods as $method) {
            $this->_processMethod($method, $this->_specification);
        }
    }

    /**
     * TODO: naming of association?
     * TODO: type of association? (NakedObjectSpecification)
     * All by FacetFactories I suppose. See the list of Facets on NOF documentation.
     */
    public function introspectAssociations()
    {
        $this->_accessors = $this->_facetProcessor->removePropertyAccessors($this->_methodRemover);
        foreach ($this->_accessors as $accessor) {
            $association = new OneToOneAssociation();
            $this->_processClass($association);
            $this->_processMethod($accessor, $association);
        }
    }

    public function introspectActions()
    {
    }

    /**
     * Currying of $this->_facetProcessor->processClass.
     */
    protected function _processClass(FacetHolder $facetHolder)
    {
        return $this->_facetProcessor->processClass($this->_reflectionClass,
                                                    $this->_methodRemover,
                                                    $facetHolder);
    }

    /**
     * Currying of $this->_facetProcessor->processMethod.
     */
    protected function _processMethod(\ReflectionMethod $method,
                                      FacetHolder $facetHolder)
    {
        return $this->_facetProcessor->processMethod($this->_reflectionClass,
                                                     $method,
                                                     $this->_methodRemover,
                                                     $facetHolder);
    }

}
