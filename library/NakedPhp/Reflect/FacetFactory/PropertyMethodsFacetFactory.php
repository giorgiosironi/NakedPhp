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
use NakedPhp\MetaModel\MethodFilteringFacetFactory;
use NakedPhp\MetaModel\NakedObjectFeatureType;
use NakedPhp\ProgModel\Facet\DisabledMethod;
use NakedPhp\ProgModel\Facet\HiddenMethod;
use NakedPhp\ProgModel\Facet\Property\ChoicesMethod;
use NakedPhp\ProgModel\Facet\Property\SetterMethod;
use NakedPhp\ProgModel\Facet\Property\ValidateMethod;
use NakedPhp\Reflect\MethodRemover;
use NakedPhp\Reflect\NameUtils;

/**
 * Used to generate the associations list and their Facets.
 */
class PropertyMethodsFacetFactory extends AbstractFacetFactory
                                  implements AssociationIdentifyingFacetFactory,
                                             MethodFilteringFacetFactory
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
    public function recognizes(\ReflectionMethod $method)
    {
        return NameUtils::startsWith($method->getName(), 'get')
            || NameUtils::startsWith($method->getName(), 'set');
    }

    /**
     * {@inheritdoc}
     */
    public function removePropertyAccessors(MethodRemover $remover)
    {
        return $remover->removeMethods('get');
    }

    /**
     * Analyze $class and $method and add produced Facets to $facetHolder.
     * $method is the method itself for Actions, the getter for Associations.
     */
    public function processMethod(\ReflectionClass $class, \ReflectionMethod $getter, MethodRemover $remover, FacetHolder $facetHolder)
    {
        if (!NameUtils::startsWith($getter->getName(), 'get')) {
            return false;
        }
        $name = str_replace('get', '', $getter->getName());
        $fieldName = lcfirst($name);
        if ($class->hasMethod($setterName = 'set' . $name)) {
            $facetHolder->addFacet(new SetterMethod($setterName));
        }
        if ($class->hasMethod($choicesName = 'choices' . $name)) {
            $facetHolder->addFacet(new ChoicesMethod($choicesName));
        }
        if ($class->hasMethod($disabledName = 'disable' . $name)) {
            $facetHolder->addFacet(new DisabledMethod($disabledName));
        }
        if ($class->hasMethod($validateName = 'validate' . $name)) {
            $facetHolder->addFacet(new ValidateMethod($validateName));
        }
        if ($class->hasMethod($hideName = 'hide' . $name)) {
            $facetHolder->addFacet(new HiddenMethod($hideName));
        }
    }
}

