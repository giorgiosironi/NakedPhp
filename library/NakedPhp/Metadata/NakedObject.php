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
 * Interface for classes that wrap a domain object.
 */
interface NakedObject extends FacetHolder
{
    /**
     * @return string
     */
    public function getClassName();

    /**
     * @return object   the wrapped instance
     */
    public function getObject();

    /**
     * Builds the list of all methods visible to the end user.
     * @return array                     NakedMethod instances
     */
    public function getMethods();

    /**
     * Returns metadata about a visible method.
     * @param string $methodName
     * @return NakedMethod          or null if not found
     */
    public function getMethod($methodName);

    /**
     * Finds out if a visible method exists.
     * @param string $methodName
     * @return boolean
     */
    public function hasMethod($methodName);

    /**
     * Magic method to call the wrapped object ones.
     * @param string $methodName
     * @param array $argument
     * @return mixed    the original result
     */
    public function __call($methodName, array $arguments = array());
}
