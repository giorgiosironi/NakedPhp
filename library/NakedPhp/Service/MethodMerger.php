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
use NakedPhp\Metadata\NakedObjectSpecification;
use NakedPhp\Metadata\NakedObjectAction;
use NakedPhp\Metadata\Facet\Action\Invocation;

/**
 * Merge services methods who take an entity as methods on the entity;
 * inject services as entity method parameters.
 * Note that hidden methods have no Invocation Facet and are strictly required
 * on the entity. Merged service methods are always invocable.
 */
class MethodMerger implements MethodCaller
{
    protected $_serviceProvider;

    public function __construct(ServiceProvider $serviceProvider = null)
    {
        $this->_serviceProvider = $serviceProvider;
    }

    /**
     * {@inheritdoc}
     * @return mixed 
     */
    public function call(NakedObject $no, $methodName, array $parameters = array())
    {
        assert('is_string($methodName)');

        $class = $no->getClass();

        if ($class->hasMethod($methodName)) {
            $parameters = $this->_addServices($class->getMethod($methodName), $parameters);
            $result = call_user_func_array(array($no, $methodName), $parameters);
        } else {
            $service = $this->_findService($methodName);
            $method = $service->getClass()->getMethod($methodName);
            $params = $this->_mergeParameters($method, $no, $parameters);
            $result = call_user_func_array(array($service, $methodName), $params);
        }

        return $result;
    }

    /**
     * Automatically provides services to inject as method parameters, removing
     * the need for the user to specify them.
     * @param NakedObjectAction $method   the method on the entity class
     * @param array $parameters     parameters passed by the user to complete
     * @return array    full array of parameters
     */
    protected function _addServices(NakedObjectAction $method, array $parameters)
    {
        $completeParameters = array(); 
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
    protected function _mergeParameters(NakedObjectAction $method, NakedObject $entity, array $parameters)
    {
        $completeParameters = array();
        foreach ($method->getParams() as $param) {
            if ($param->getType() == $entity->getClassName()) {
                $completeParameters[] = $entity->getObject();
            } else {
                $completeParameters[] = array_shift($parameters);
            }
        }
        assert('$parameters == array()');

        return $completeParameters;
    }

    /**
     * {@inheritdoc}
     * Merges service methods and rewrite metadata for entity methods who 
     * need a service, currying them.
     */
    public function getApplicableMethods(NakedObjectSpecification $class)
    {
        $servicesMethods = array();
        foreach ($this->_getAllServicesMethods() as $methodName => $method) {
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
    protected function _getAllServicesMethods()
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
    protected function _buildFakeMethod(NakedObjectAction $method, NakedObjectSpecification $class)
    {
        $methodName = (string) $method;
        $newParams = array();
        foreach ($method->getParams() as $key => $param) {
            if ($param->getType() != $class->getClassName()) { 
                $newParams[$key] = $param;
            }
        }
        $newMethod = new NakedObjectAction($methodName, $newParams, $method->getReturn());

        if ($method->getFacet('Action\Invocation')) {
            $newMethod->addFacet(new Invocation($methodName));
        }

        return $newMethod;
    }

    /**
     * {@inheritdoc}
     * Convenience method.
     */
    public function getMethod(NakedObjectSpecification $class, $methodName)
    {
        $methods = $this->_getAllMethods($class);
        return $methods[$methodName];
    }

    /**
     * {@inheritdoc}
     * Convenience method.
     */
    public function hasMethod(NakedObjectSpecification $class, $methodName)
    {
        $methods = $this->_getAllMethods($class);
        return isset($methods[$methodName]);
    }

    /**
     * Returns all methods, visible and hidden ones. To use internally.
     * @return array NakedObjectAction instances
     */
    protected function _getAllMethods(NakedObjectSpecification $class)
    {
        $methods = $this->getApplicableMethods($class);
        return $methods;
    }
}
