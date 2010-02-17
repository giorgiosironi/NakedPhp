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

class PhpIntrospectorFactory implements IntrospectorFactory
{
    protected $_facetProcessor;
    protected $_metaModelFactory;    

    public function __construct(FacetProcessor $facetProcessor = null,
                                MetaModelFactory $metaModelFactory = null)
    {
        $this->_facetProcessor = $facetProcessor;
        $this->_metaModelFactory = $metaModelFactory;
    }

    public function getIntrospector(NakedObjectSpecification $specification)
    {
        return new PhpIntrospector($specification,
                                   $this->_facetProcessor,
                                   $this->_metaModelFactory);
    }
}
