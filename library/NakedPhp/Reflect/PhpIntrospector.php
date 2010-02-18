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
use NakedPhp\MetaModel\NakedObjectFeatureType;
use NakedPhp\ProgModel\OneToOneAssociation;
use NakedPhp\ProgModel\PhpAction;
use NakedPhp\ProgModel\PhpActionParameter;
use NakedPhp\ProgModel\PhpSpecification;

/**
 * This class uses the work of the FacetProcessor to fill in the Specification
 * with objects created from the MetaModelFactory.
 */
class PhpIntrospector
{
    protected $_specification;
    protected $_facetProcessor;
    protected $_metaModelFactory;
    protected $_reflectionClass;
    protected $_methodRemover;

    public function __construct(PhpSpecification $specification = null,
                                FacetProcessor $facetProcessor = null,
                                MetaModelFactory $metaModelFactory = null)
    {
        $this->_specification = $specification;
        $this->_facetProcessor = $facetProcessor;
        $this->_metaModelFactory = $metaModelFactory;
        // FIX: real work; probably necessary since all methods need reflection objects to work
        // move in init() method
        if ($this->_specification) {
            $this->_reflectionClass = new \ReflectionClass($this->_specification->getClassName());
            $this->_methods = new \ArrayObject($this->_reflectionClass->getMethods());
            $this->_methodRemover = new ArrayObjectMethodRemover($this->_methods);
        }
    }

    /**
     * Initializes the class-level Facets on $this->_specification.
     * FIX: should call only the FF that recognize CLASS
     * @return void
     */
    public function introspectClass()
    {
        $this->_processClass($this->_specification, NakedObjectFeatureType::OBJECT);

        foreach ($this->_methods as $method) {
            $this->_processMethod($method, $this->_specification, NakedObjectFeatureType::OBJECT);
        }
    }

    /**
     * Initializes the associations list on $this->_specification,
     * with the respective Facets.
     * TODO: type of association? (NakedObjectSpecification) will be factored out
     * All by FacetFactories I suppose. See the list of Facets on NOF documentation.
     * FIX: should call only the FFs that recognize PROPERTY
     * @return void
     */
    public function introspectAssociations()
    {
        $associations = array();
        $this->_accessors = $this->_facetProcessor->removePropertyAccessors($this->_methodRemover);
        foreach ($this->_accessors as $accessor) {
            $association = $this->_metaModelFactory->createAssociation($accessor);
            $identifier = NameUtils::baseName($accessor->getName());
            $this->_processClass($association, NakedObjectFeatureType::PROPERTY);
            $this->_processMethod($accessor, $association, NakedObjectFeatureType::PROPERTY);
            $associations[$identifier] = $association;
        }
        $this->_specification->initAssociations($associations);
    }

    /**
     * Initializes the list of actions on $this->_specification, 
     * including the respective facets.
     * FIX: should call only the FF that recognize ACTION or ACTION_PARAM
     * @return void
     */
    public function introspectActions()
    {
        $actions = array();
        foreach ($this->_methods as $method) {
            if ($this->_facetProcessor->recognizes($method)) {
                continue;
            }
            $action = $this->_metaModelFactory->createAction($method);
            $this->_processClass($action, NakedObjectFeatureType::ACTION);
            foreach ($this->_methods as $collaboratorCandidate) {
                $this->_processMethod($collaboratorCandidate, $action, NakedObjectFeatureType::ACTION);
            }
            $name = $method->getName();
            $actions[$name] = $action;
        }
        $this->_specification->initObjectActions($actions);
    }

    /**
     * Currying of $this->_facetProcessor->processClass.
     */
    protected function _processClass(FacetHolder $facetHolder, $featureType)
    {
        return $this->_facetProcessor->processClass($this->_reflectionClass,
                                                    $this->_methodRemover,
                                                    $facetHolder,
                                                    $featureType);
    }

    /**
     * Currying of $this->_facetProcessor->processMethod.
     */
    protected function _processMethod(\ReflectionMethod $method,
                                      FacetHolder $facetHolder,
                                      $featureType)
    {
        return $this->_facetProcessor->processMethod($this->_reflectionClass,
                                                     $method,
                                                     $this->_methodRemover,
                                                     $facetHolder,
                                                     $featureType);
    }

}
