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
 * @package    NakedPhp_Service
 */

namespace NakedPhp\Service;
use NakedPhp\Metadata\NakedObject;
use NakedPhp\Metadata\NakedClass;
use NakedPhp\Metadata\NakedMethod;

interface MethodCaller
{
    /**
     * Call a method on a NakedObject.
     * @param NakedObject $no       object to search the method on
     * @param string $methodName
     * @param array $parameters
     * @return mixed
     */
    public function call(NakedObject $no, $methodName, array $parameters = array());

    /**
     * Builds the list of all methods visible to the end user.
     * @param NakedClass $class    the type of the entity considered
     * @return array                     NakedMethod instances
     */
    public function getApplicableMethods(NakedClass $class);

    /**
     * Returns metadata about a method.
     * @param NakedClass $class     class to search the method on
     * @param string $methodName
     * @return NakedMethod          or null if not found
     */
    public function getMethod(NakedClass $no, $methodName);

    /**
     * Finds out if a method exists.
     * @param NakedClass $class     class to search the method on
     * @param string $methodName
     * @return boolean
     */
    public function hasMethod(NakedClass $no, $methodName);
}
