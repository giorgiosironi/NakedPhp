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
 * Fills in the Specification objects.
 */
interface Introspector
{
    /**
     * @return NakedObjectSpecification
     */
    public function getSpecification();

    /**
     * Initializes the class-level Facets on the specification.
     * @return void
     */
    public function introspectClass();

    /**
     * Initializes the associations list on the specification,
     * with the respective Facets.
     * @return void
     */
    public function introspectAssociations();

    /**
     * Initializes the list of actions on the specification, 
     * including the respective facets.
     * @return void
     */
    public function introspectActions();
}
