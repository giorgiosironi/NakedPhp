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

namespace NakedPhp\Metadata\Facet;
use NakedPhp\Metadata\Facet;
use NakedPhp\Metadata\NakedObject;

class Hidden implements Facet
{
    /**
     * @var string property name or method name
     * @example 'myField'
     */
    private $_featureName;
    
    /**
     * @param string
     */
    public function __construct($featureName)
    {
        $this->_featureName = $featureName;
    }

    /**
     * @return string   false in case feature is not disabled
     */
    public function hiddenReason(NakedObject $no)
    {
        $methodName = 'hide' . ucfirst($this->_featureName);
        return $no->$methodName();
    }

    /**
     * {@inheritdoc}
     */
    public function facetType()
    {
        return 'Hidden';
    }
}
