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
use NakedPhp\MetaModel\NakedObjectSpecification;
use NakedPhp\Reflect\Introspector\PhpClassIntrospector;
use NakedPhp\Reflect\Introspector\PhpTypeIntrospector;

class PhpIntrospectorFactory implements IntrospectorFactory
{
    protected $_specificationFactories;
    protected $_facetProcessor;
    protected $_metaModelFactory;    

    public function __construct(array $specificationFactories = null,
                                FacetProcessor $facetProcessor = null,
                                MetaModelFactory $metaModelFactory = null)
    {
        $this->_specificationFactories = $specificationFactories;
        $this->_facetProcessor         = $facetProcessor;
        $this->_metaModelFactory       = $metaModelFactory;
    }

    /**
     * TODO: move here PhpClassIntrospector::__construct() code
     */
    public function getIntrospectors()
    {
        $specifications = array();
        foreach ($this->_specificationFactories as $specFactory) {
            $specifications += $specFactory->getSpecifications();
        }

        $introspectors = array();
        foreach ($specifications as $name => $specification) {
            if (ctype_upper($name{0})) {
                $introspectors[$name] = new PhpClassIntrospector($specification,
                                                        $this->_facetProcessor,
                                                        $this->_metaModelFactory);
            } else {
                $introspectors[$name] = new PhpTypeIntrospector($specification);
            }
        }
        return $introspectors;
    }
}
