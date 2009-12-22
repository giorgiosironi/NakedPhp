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

/*final*/ class ServiceCollection
{
    public function __construct(ServiceProvider $provider = null)
    {
    }

    /**
     * @param NakedEntitySpecification $class    the type of the entity considered
     * @return array                     NakedMethod instances
     */
    public function getApplicableMethods(NakedEntitySpecification $class)
    {
        return array();
    }

    public function call(NakedMethod $method, NakedBareEntity $entity, array $parameters = array())
    {
        return $entity;
    }
}
