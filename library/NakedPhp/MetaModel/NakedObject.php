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
 * @package    NakedPhp_MetaModel
 */

namespace NakedPhp\MetaModel;

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
     * @return bool
     */
    public function isService();

    /**
     * @return object   the wrapped instance
     */
    public function getObject();

    /**
     * @return bool     true if the class is the same and the wrapped object
     *                  is the same (===)
     */
    public function equals(NakedObject $another);

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

    /**
     * Wraps $object in another instance of the implementor class.
     * Prototype pattern implementation.
     * @param mixed $object
     * @param NakedObjectSpecification $spec
     * @return NakedObject
     */
    public function createNewInstance($object, NakedObjectSpecification $spec);

    /**
     * @param mixed $object     the new object to wrap
     */
    public function replace($object);
}
