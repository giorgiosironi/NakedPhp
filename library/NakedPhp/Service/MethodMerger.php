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
use NakedPhp\Metadata\NakedEntityClass;

class MethodMerger
{
    public function __construct(ServiceProvider $serviceProvider = null, NakedFactory $nakedFactory = null)
    {
        $this->_nakedFactory = $nakedFactory;
    }

    public function call(NakedObject $no, $method, array $parameters = array())
    {
        $result = call_user_func_array(array($no, $method), $parameters);
        if (is_object($result)) {
            return $this->_nakedFactory->create($result);
        } else {
            return $result;
        }
    }

    /**
     * @param NakedEntityClass $class    the type of the entity considered
     * @return array                     NakedMethod instances
     */
    public function getApplicableMethods(NakedEntityClass $class)
    {
        return $class->getMethods();
    }
}
