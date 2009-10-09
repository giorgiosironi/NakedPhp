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
use NakedPhp\Metadata\NakedObject;
use NakedPhp\Metadata\NakedEntity;

class Controller extends \Zend_Controller_Action
{
    /**
     * @var NakedPhp\Factory    creates nakedphp object
     */
    private $_factory;

    /**
     * @var NakedPhp\Service\EntityContainer   contains entity objects
     */
    private $_entityContainer;

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
        $this->view->session = $this->_entityContainer = $this->_factory->getEntityContainer();
        $this->view->services = $this->_services = $this->_factory->getServiceIterator();
        $this->_methodMerger = $this->_factory->getMethodMerger();

        $objectKey = $this->_request->getParam('object');
        if ($objectKey !== null) {
            if ($this->_request->getParam('type') == 'service') {
                $provider = $this->_factory->getServiceProvider();
                $this->_object = $provider->getService($objectKey);
                $this->view->methods = $this->_object->getClass()->getMethods();
            } else {
                $this->_object = $this->_entityContainer->get($objectKey);
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
            if ($this->_object) {
                $this->renderScript('segments/methods.phtml', 'nakedphp_methods');
            }
        }
    }

    public final function indexAction()
    {
        echo "Hello world from NakedPhp!";
    }

    /**
     * This action shows a NakedEntity or NakedService object.
     */
    public final function viewAction()
    {
    }

    /**
     * This action allows editing of a NakedEntity object.
     */
    public final function editAction()
    {
        $formBuilder = $this->_factory->getFieldsFormBuilder();
        $form = $formBuilder->createForm($this->_object->getClass()->getFields());
        $stateManager = $this->_factory->getStateManager()
                                       ->populateOptions($form)
                                       ->setFormState($form, $this->_object);
        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            $state = $stateManager->setEntityState($this->_object, $form);
            $this->_redirectToObject($this->_object);
        } else {
            $this->view->form = $form;
        }
    }

    /**
     * This action allows to call a method on a NakedEntity or NakedService object.
     */
    public final function callAction()
    {
        $methodName = $this->_request->getParam('method');
        $this->view->methodName = $methodName;
        $method = $this->_methodMerger->getMethod($this->_object, $methodName);
        if (count($method->getParams())) {
            $formBuilder = $this->_factory->getMethodFormBuilder();
            $form = $formBuilder->createForm($method);
            if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
                $parameters = $this->_request->getPost();
            } else {
                $this->view->form = $form;
                return; 
            }
        } else {
            $parameters = array();
        }

        $result = $this->_methodMerger->call($this->_object, (string) $method, $parameters);
        if (is_object($result)) {
            $this->_redirectToObject($result);
        } else {
            $this->view->result = $result;
        }
    }

    /**
     * This method redirects to the view action of a NakedEntity or NakedService object.
     * @param NakedObject $no
     */
    protected function _redirectToObject(NakedObject $no)
    {
        if ($no instanceof NakedEntity) {
            $index = $this->_entityContainer->add($no);
            $type = 'entity';
        } else {
            $index = $no->getClass();
            $type = 'service';
        }
        $params = array(
            'type' => $type,
            'object' => $index
        );
        $this->_helper->Redirector('view', null, null, $params);
    }
}
