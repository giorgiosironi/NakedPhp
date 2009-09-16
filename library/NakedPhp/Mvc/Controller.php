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
 * @package    NakedPhp_Mvc
 */

namespace NakedPhp\Mvc;

class Controller extends \Zend_Controller_Action
{
    /**
     * @var NakedPhp\Factory    creates nakedphp object
     */
    private $_factory;

    /**
     * @var NakedPhp\Service\SessionContainer   contains entity objects
     */
    private $_sessionContainer;

    /**
     * @var NakedPhp\Service\ServiceIterator    lists services
     */
    private $_services;

    /**
     * @var NakedPhp\Service\MethodMerger   merges service methods in entities
     */
    private $_methodMerger;

    /**
     * @var NakedObject     the current object
     */
    private $_object;

    public final function preDispatch()
    {
        $this->_factory = new \NakedPhp\Factory();
        $this->view->session = $this->_sessionContainer = $this->_factory->getSessionContainer();
        $this->view->services = $this->_services = $this->_factory->getServiceIterator();
        $this->_methodMerger = $this->_factory->getMethodMerger();

        $objectKey = $this->_request->getParam('object');
        if ($objectKey !== null) {
            if ($this->_request->getParam('type') == 'service') {
                $provider = $this->_factory->getServiceProvider();
                $this->_object = $provider->getService($objectKey);
                $this->view->methods = $this->_object->getClass()->getMethods();
            } else {
                $this->_object = $this->_sessionContainer->get($objectKey);
                $this->view->methods = $this->_methodMerger->getApplicableMethods($this->_object->getClass());
            }
            $this->view->object = $this->_object;
        }
    }

    public final function postDispatch()
    {
        $paths = $this->view->getScriptPaths();
        $originalPath = array_shift($paths);
        $this->view->setScriptPath($paths);
        $this->view->addScriptPath(__DIR__ . '/views/scripts/');
        $this->view->addHelperPath(realpath(__DIR__) . '/View/Helper', 'NakedPhp\\Mvc\\View\\Helper\\');

        if (!$this->_helper->ViewRenderer->getNoRender()) {
            $this->render(null, null, true);
            $this->renderScript('segments/session.phtml', 'nakedphp_session');
            $this->renderScript('segments/services.phtml', 'nakedphp_services');
            $this->renderScript('segments/methods.phtml', 'nakedphp_methods');
        }
    }

    public final function indexAction()
    {
        echo "Hello world from NakedPhp!";
    }

    public final function viewAction()
    {
    }

    public final function editAction()
    {
        throw new \Exception('Not yet implemented.');
    }

    public final function callAction()
    {
        $method = $this->_request->getParam('method');
        $parameters = $this->_request->getPost();
        $this->_methodMerger->call($this->_object, $method, $parameters);
    }

    protected function _redirectToObject(NakedObject $no)
    {
        throw new Exception('Not yet implemented.');
    }
}
