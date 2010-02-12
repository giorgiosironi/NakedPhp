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
use NakedPhp\MetaModel\FacetFactory;
use NakedPhp\MetaModel\FacetHolder;
use NakedPhp\Reflect\MethodRemover;

abstract class AbstractFacetFactory implements FacetFactory
{
    public function getFeatureTypes()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function processClass(\ReflectionClass $class, MethodRemover $remover, FacetHolder $facetHolder)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function processMethod(\ReflectionClass $class, \ReflectionMethod $getter, MethodRemover $remover, FacetHolder $facetHolder)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function processParams(\ReflectionMethod $method, $paramNum, FacetHolder $facetHolder)
    {
        return false;
    }
}
