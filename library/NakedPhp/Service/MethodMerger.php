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
use NakedPhp\Metadata\NakedService;
use NakedPhp\Metadata\NakedMethod;

class MethodMerger
{
    protected $_nakedFactory;
    protected $_serviceProvider;

    public function __construct(ServiceProvider $serviceProvider = null, NakedFactory $nakedFactory = null)
    {
        $this->_nakedFactory = $nakedFactory;
        $this->_serviceProvider = $serviceProvider;
    }

    /**
     * @param NakedObject $no       object to search the method on
     * @param string $methodName
     * @param array $parameters
     * @return NakedObject  if the result is an object it will be wrapped.
     *                      Otherwise, it will be returned as-is.
     */
    public function call(NakedObject $no, $methodName, array $parameters = array())
    {
        assert('is_string($methodName)');

        if ($no->hasMethod($methodName)) {
            $parameters = $this->_addServices($no->getClass()->getMethod($methodName), $parameters);
            $result = call_user_func_array(array($no, $methodName), $parameters);
        } else {
            $service = $this->_findService($methodName);
            $method = $service->getClass()->getMethod($methodName);
            $params = $this->_mergeParameters($method, $no, $parameters);
            $result = call_user_func_array(array($service, $methodName), $params);
        }

        return $this->_treatResult($result);
    }

    /**
     * Automatically provides services to inject as method parameters, removing
     * the need for the user to specify them.
     * @param NakedMethod $method   the method on the entity class
     * @param array $parameters     parameters passed by the user to complete
     * @return array    full array of parameters
     */
    protected function _addServices(NakedMethod $method, array $parameters)
    {
        $completeParameters = Array(); 
        $serviceClasses = $this->_serviceProvider->getServiceClasses();
        foreach ($method->getParams() as $name => $param) {
            $type = $param->getType();
            if (isset($serviceClasses[$type])) {
                $completeParameters[$name] = $this->_serviceProvider->getService($type);
            } else {
                $completeParameters[$name] = array_shift($parameters);
            }
        }
        assert('$parameters == array()');
        return $completeParameters;
    }

    /**
     * Merges $entity in $parameters according to $method metadata.
     * Used for providing a service method as if it were on an entity, 
     * automatically injecting the latter.
     * TODO: factoring out protected methods in a ParameterManager/Parameters class.
     */
    protected function _mergeParameters(NakedMethod $method, NakedObject $entity, array $parameters)
    {
        $completeParameters = array();
        foreach ($method->getParams() as $param) {
            if ($param->getType() == $entity->getClassName()) {
                $completeParameters[] = $entity->unwrap();
            } else {
                $completeParameters[] = array_shift($parameters);
            }
        }
        assert('$parameters == array()');

        return $completeParameters;
    }

    /**
     * Wraps a method result in a NakedObject instance if it is not scalar.
     * @param mixed
     * @return mixed
     */
    protected function _treatResult($result)
    {
        if (is_object($result)) {
            return $this->_nakedFactory->create($result);
        } else {
            return $result;
        }
    }

    /**
     * @param NakedClass $class    the type of the entity considered
     * @return array                     NakedMethod instances
     */
    public function getApplicableMethods(NakedClass $class)
    {
        $servicesMethods = array();
        foreach ($this->_getAllMethods() as $methodName => $method) {
            foreach ($method->getParams() as $param) {
                if ($param->getType() == $class->getClassName()) {
                    $servicesMethods[$methodName] = $this->_buildFakeMethod($method, $class);
                    break;
                }
            }
        }

        $classMethods = array();
        $serviceClasses = $this->_serviceProvider->getServiceClasses();
        foreach ($class->getMethods() as $methodName => $method) {
            foreach ($method->getParams() as $param) {
                $type = $param->getType();
                if (isset($serviceClasses[$type])) {
                    $classMethods[$methodName] = $this->_buildFakeMethod($method, $serviceClasses[$type]);
                    break;
                }
            }
            if (!isset($classMethods[$methodName])) {
                $classMethods[$methodName] = $method;
            }
        }

        return $classMethods + $servicesMethods;
    }

    /**
     * @return array    all methods from services indexed by name
     */
    protected function _getAllMethods()
    {
        $methods = array();
        foreach ($this->_serviceProvider->getServiceClasses() as $serviceClass) {
            foreach ($serviceClass->getMethods() as $method) {
                $methodName = (string) $method;
                $methods[$methodName] = $method;
            }
        }
        return $methods;
    }

    /**
     * @param string $methodName    method name to search for
     * @return NakedService         the service name
     */
    protected function _findService($methodName)
    {
        foreach ($this->_serviceProvider->getServiceClasses() as $serviceName => $serviceClass) {
            $methods = $serviceClass->getMethods();
            if (isset($methods[$methodName])) {
                return $this->_serviceProvider->getService($serviceName);
            }
        }
    }

    /**
     * Builds metadata for a method leaving out the $class parameter, which
     * will be then automatically passed.
     */
    protected function _buildFakeMethod(NakedMethod $method, NakedClass $class)
    {
        $newParams = array();
        foreach ($method->getParams() as $key => $param) {
            if ($param->getType() != $class->getClassName()) { 
                $newParams[$key] = $param;
            }
        }
        return new NakedMethod((string) $method, $newParams, $method->getReturn());
    }

    /**
     * @param NakedObject $no       object to search the method on
     * @param string $methodName
     * @return NakedMethod
     */
    public function getMethod(NakedObject $no, $methodName)
    {
        $methods = $this->getApplicableMethods($no->getClass());
        return $methods[$methodName];
    }
}
