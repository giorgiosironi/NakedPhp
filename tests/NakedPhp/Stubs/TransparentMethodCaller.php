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
 * @package    NakedPhp_Stubs
 */

namespace NakedPhp\Stubs;
use NakedPhp\Service\MethodCaller;
use NakedPhp\Metadata\NakedObject;
use NakedPhp\Metadata\NakedClass;
use NakedPhp\Metadata\NakedService;
use NakedPhp\Metadata\NakedMethod;

/**
 * This stub for the MethodCaller interface is used in tests to substitute
 * MethodMerger. It assumes there are no services and the entities have methods
 * that can be called on their own, without passing services as parameters.
 */
class TransparentMethodCaller implements MethodCaller
{
    public function call(NakedObject $no, $methodName, array $parameters = array())
    {
        return call_user_func_array(array($no, $methodName), $parameters);
    }

    public function getApplicableMethods(NakedClass $class)
    {
        return $class->getMethods();
    }

    public function getMethod(NakedClass $class, $methodName)
    {
        $methods = $this->getApplicableMethods($class);
        $methods += $class->getHiddenMethods();
        return $methods[$methodName];
    }

    public function hasMethod(NakedClass $class, $methodName)
    {
        $methods = $this->getApplicableMethods($class);
        $methods += $class->getHiddenMethods();
        return isset($methods[$methodName]);
    }
}
