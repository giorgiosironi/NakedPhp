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

/**
 * Wraps properties about a domain class like fields, methods and metadata.
 */
class NakedClass
{
    /**
     * @var array names of properties
     */
    private $_fields;

    /**
     * @var array available methods
     */
    private $_methods;

    public function __construct(array $fields = array(), array $methods = array())
    {
        $this->_fields = $fields;
        $this->_methods = $methods;
    }

    public function getFields()
    {
        return $this->_fields;
    }

    public function getMethods()
    {
        return $this->_methods;
    }
}
