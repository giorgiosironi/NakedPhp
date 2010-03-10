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
 * @package    NakedPhp_ProgModel
 */

namespace NakedPhp\ProgModel\Facet;
use NakedPhp\MetaModel\Facet\Collection;
use NakedPhp\MetaModel\Facet\Collection\TypeOf;
use NakedPhp\MetaModel\NakedObject;

class CollectionArray implements Collection
{
    protected $_typeOfFacet;

    public function __construct(TypeOf $typeOfFacet = null)
    {
        $this->_typeOfFacet = $typeOfFacet;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(NakedObject $nakedObjectRepresentingCollection)
    {
        $array = $nakedObjectRepresentingCollection->getObject();
        $wrappedItemsArray = array();
        foreach ($array as $key => $value) {
            $wrappedItemsArray[$key] = $nakedObjectRepresentingCollection->createNewInstance($value, $this->_typeOfFacet->valueSpec());
        }
        return $wrappedItemsArray;
    }

    /**
     * {@inheritdoc}
     */
    public function iterator(NakedObject $nakedObjectRepresentingCollection)
    {
        return new \ArrayIterator($this->toArray($nakedObjectRepresentingCollection));
    }

    /**
     * {@inheritdoc}
     */
    public function facetType()
    {
        return 'Collection';
    }
}
