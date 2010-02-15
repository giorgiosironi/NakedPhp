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

namespace NakedPhp\ProgModel\Facet\Property;
use NakedPhp\MetaModel\Facet\Property\Choices;
use NakedPhp\MetaModel\NakedObject;

class ChoicesMethod implements Choices
{
    /**
     * @var string method name
     * @example 'choicesMyField'
     */
    private $_methodName;
    
    /**
     * @param string
     */
    public function __construct($methodName)
    {
        $this->_methodName = $methodName;
    }

    /**
     * @return array    possible values for the field
     */
    public function getChoices(NakedObject $no)
    {
        $methodName = $this->_methodName;
        return $no->$methodName();
    }

    /**
     * {@inheritdoc}
     */
    public function facetType()
    {
        return 'Property\Choices';
    }
}
