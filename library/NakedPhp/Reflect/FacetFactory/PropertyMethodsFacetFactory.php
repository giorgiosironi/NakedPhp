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

namespace NakedPhp\Reflect\FacetFactory;
use NakedPhp\MetaModel\AssociationIdentifyingFacetFactory;
use NakedPhp\MetaModel\FacetHolder;
use NakedPhp\MetaModel\NakedObjectFeatureType;
use NakedPhp\Reflect\MethodRemover;

class PropertyMethodsFacetFactory implements AssociationIdentifyingFacetFactory
{
    /**
     * {@inheritdoc}
     */
    public function getFeatureTypes()
    {
        return array(NakedObjectFeatureType::PROPERTY);
    }

    /**
     * {@inheritdoc}
     */
    public function removePropertyAccessors(MethodRemover $remover)
    {
        return $remover->removeMethods('get');
    }

    /**
     * {@inheritdoc}
     */
    public function processClass(\ReflectionClass $class, FacetHolder $facetHolder)
    {
        return false;
    }

    /**
     * Analyze $class and $method and add produced Facets to $facetHolder.
     * $method is the method itself for Actions, the getter for Associations.
     */
    public function processMethod(\ReflectionClass $class, \ReflectionMethod $method, FacetHolder $facetHolder) {}

    /**
     * {@inheritdoc}
     */
    public function processParams(\ReflectionMethod $method, $paramNum, FacetHolder $facetHolder)
    {
        return false;
    }
}

