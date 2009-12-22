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
 * Interface for classes that wrap an entity object.
 */
interface NakedEntity extends NakedObject, \IteratorAggregate
{
    /**
     * @return NakedEntitySpecification
     */
    public function getClass();

    /**
     * @return array    field names are keys
     */
    public function getState();

    /**
     * @param array $data   field names are keys; works also with objects and
     *                      objects wrapped in NakedBareEntity
     */
    public function setState(array $data);

    /**
     * @param string $name
     * @return NakedField
     */
    public function getField($name);
}
