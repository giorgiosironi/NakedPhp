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

/**
 * Abstraction over a set of methods which can be
 * removed selectively and used for Facet production purposes.
 */
interface MethodRemover
{
    /**
     * @param string $prefix    if contained in method names cause their removal
     * @return array            removed ReflectionMethod instances
     */
    public function removeMethods($prefix);
}
