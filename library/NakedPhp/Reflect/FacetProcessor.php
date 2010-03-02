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
use NakedPhp\Reflect\MethodRemover;

/**
 * Aggregates FacetFactory instances.
 */
interface FacetProcessor
{
    /**
     * @return array    ReflectionMethod instances
     */
    public function removePropertyAccessors(MethodRemover $remover);

    /**
     * @return bool
     */
    public function recognizes(\ReflectionMethod $method);

    /**
     * @param string $feaureType     a NakedObjectFeatureType constant
     */
    public function processClass(\ReflectionClass $class, MethodRemover $remover, FacetHolder $holder, $featureType = null);

    /**
     * @param string $featureType     a NakedObjectFeatureType constant
     */
    public function processMethod(\ReflectionClass $class, \ReflectionMethod $method, MethodRemover $remover, FacetHolder $holder, $featureType = null);
}
