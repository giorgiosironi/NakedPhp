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
    private $_factory;
    private $_sessionContainer;
    private $_singletons;
    private $_methodMerger;

    public final function preDispatch()
    {
        $this->_factory = new \NakedPhp\Factory();
        $this->view->session = $this->_sessionContainer = $this->_factory->getSessionContainer();
        $this->view->singletons = $this->_singletons = $this->_factory->getSingletons();
        $this->_methodMerger = $this->_factory->getMethodMerger();
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
            $this->renderScript('segments/singletons.phtml', 'nakedphp_singletons');
        }
    }

    public final function indexAction()
    {
        echo "Hello world from NakedPhp!";
    }

    public final function viewAction()
    {
        $objectKey = $this->_request->getParam('object');
        $this->view->object = $this->_sessionContainer->get($objectKey);
        throw new Exception('Not yet implemented.');
    }

    public final function editAction()
    {
        throw new Exception('Not yet implemented.');
    }

    public final function callAction()
    {
        throw new Exception('Not yet implemented.');
        $object = $this->_request->getParam('object');
        $method = $this->_request->getParam('method');
        $parameters = $this->_request->getPost();
        $this->_methodMerger->call($object, $method, $parameters);
    }

    protected function _redirect(NakedObject $no)
    {
        throw new Exception('Not yet implemented.');
    }
}
