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
use NakedPhp\MetaModel\FacetHolder;
use NakedPhp\MetaModel\MethodFilteringFacetFactory;
use NakedPhp\MetaModel\NakedObjectAction;
use NakedPhp\MetaModel\NakedObjectFeatureType;
use NakedPhp\ProgModel\Facet\Action\InvocationMethod;
use NakedPhp\Reflect\MethodRemover;

/**
 * Used to generate the associations list and their Facets.
 */
class ActionMethodsFacetFactory extends AbstractFacetFactory
                                implements MethodFilteringFacetFactory
{
    /**
     * {@inheritdoc}
     */
    public function getFeatureTypes()
    {
        return array(NakedObjectFeatureType::ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function recognizes(\ReflectionMethod $method)
    {
        return false;
    }

    /**
     * Adds Action\Invocation Facet to the method.
     */
    public function processMethod(\ReflectionClass $class, \ReflectionMethod $method, MethodRemover $remover, FacetHolder $facetHolder)
    {
        // FIX: HACK
        if ($facetHolder instanceof NakedObjectAction) {
            $returnType = $facetHolder->getReturnType();
            $invocation = new InvocationMethod($method->getName(), $returnType);
            $facetHolder->addFacet($invocation);
        }
    }
}

