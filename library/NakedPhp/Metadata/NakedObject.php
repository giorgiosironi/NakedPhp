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
interface NakedObject extends ActionContainer, AssociationContainer, FacetHolder
{
    /**
     * @return NakedObjectSpecification
     */
    public function getSpecification();

    /**
     * @return string
     */
    public function getClassName();

    /**
     * @return boolean
     */
    public function isService();

    /**
     * @return object   the wrapped instance
     */
    public function getObject();

    /**
     * FIX: does not belong to all objects.
     * @return array    field names are keys
     */
    public function getState();

    /**
     * FIX: does not belong to all objects.
     * @param array $data   field names are keys; works also with objects and
     *                      objects wrapped in NakedBareObject
     */
    public function setState(array $data);

    /**
     * Magic method to call the wrapped object ones.
     * @param string $methodName
     * @param array $argument
     * @return mixed    the original result
     */
    public function __call($methodName, array $arguments = array());
}
