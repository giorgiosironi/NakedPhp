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
 * @package    NakedPhp_Metadata
 */

namespace NakedPhp\Metadata;


abstract class AbstractFacetHolder implements FacetHolder
{
    /**
     * @var array of Facet instances, indexed by type
     */
    protected $_facets;

    /**
     * {@inheritdoc}
     */
    public function addFacet(Facet $facet)
    {
        $type = $facet->facetType();
        if (!isset($this->_facets[$type])) {
            $this->_facets[$type] = array();
        }
        $this->_facets[$type][] = $facet;
    }

    /**
     * {@inheritdoc}
     */
    public function getFacet($type)
    {
        if (isset($this->_facets[$type])) {
            return current($this->_facets[$type]);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFacets($type)
    {
        if (isset($this->_facets[$type])) {
            return $this->_facets[$type];
        }
        return array();
    }
}
