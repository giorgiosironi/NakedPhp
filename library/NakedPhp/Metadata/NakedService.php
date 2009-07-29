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
 * Wraps properties about a service class. A service class should have
 * a stateless interface and be composed of many methods that takes
 * as parameters scalar values or domain objects.
 */
final class NakedService
{
    /**
     * @var array available methods
     */
    private $_methods;

    public function __construct(array $methods = array())
    {
        $this->_methods = $methods;
    }

    public function getMethods()
    {
        return $this->_methods;
    }
}
