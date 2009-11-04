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
use NakedPhp\Metadata\NakedBareEntity;
use NakedPhp\Metadata\NakedCompleteEntity;

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
     * @var NakedObject     the current object
     */
    private $_completeObject;

    /**
     * @var NakedClass      the class of the current object
     */
    private $_class;

    public final function preDispatch()
    {
        $this->_factory = new \NakedPhp\Factory();
        $this->view->session = $this->_entityContainer = $this->_factory->getEntityContainer();
        $this->view->services = $this->_services = $this->_factory->getServiceIterator();

        $objectKey = $this->_request->getParam('object');
        if ($objectKey !== null) {
            if ($this->_request->getParam('type') == 'service') {
                $provider = $this->_factory->getServiceProvider();
                $this->_completeObject = $provider->getService($objectKey);
            } else {
                $object = $this->_entityContainer->get($objectKey);
                $this->_completeObject = $this->_factory->createCompleteEntity($object);
            }
            $this->view->methods = $this->_completeObject->getMethods();
            $this->_class = $this->_completeObject->getClass();
            $this->view->object = $this->_completeObject;
        }
    }

    public final function postDispatch()
    {
        $paths = $this->view->getScriptPaths();
        $originalPath = array_shift($paths);
        $this->view->setScriptPath($paths);
        $this->view->addScriptPath(__DIR__ . '/views/scripts/');
        $this->view->addScriptPath($originalPath);
        $this->view->addHelperPath(realpath(__DIR__) . '/View/Helper', 'NakedPhp\\Mvc\\View\\Helper\\');

        if (!$this->_helper->ViewRenderer->getNoRender()) {
            $this->render(null, null, true);
            $this->renderScript('segments/session.phtml', 'nakedphp_session');
            $this->renderScript('segments/services.phtml', 'nakedphp_services');
            if ($this->_completeObject) {
                $this->renderScript('segments/methods.phtml', 'nakedphp_methods');
            }
        }
    }

    public final function indexAction()
    {
        echo "Hello world from NakedPhp!";
    }

    /**
     * This action shows a NakedBareEntity or NakedService object.
     */
    public final function viewAction()
    {
    }

    /**
     * This action allows editing of a NakedBareEntity object.
     */
    public final function editAction()
    {
        $formBuilder = $this->_factory->getFieldsFormBuilder();
        $form = $formBuilder->createForm($this->_completeObject, $this->_class->getFields());
        $stateManager = $this->_factory->getStateManager()
                                       ->populateOptions($form)
                                       ->setFormState($form, $this->_completeObject);
        if ($this->_request->isPost() && $form->isValidPartial($this->_request->getPost())) {
            $state = $stateManager->setEntityState($this->_completeObject, $form);
            $this->_redirectToObject($this->_completeObject);
        } else {
            $this->view->form = $form;
        }
    }

    /**
     * This action allows to call a method on a NakedBareEntity or NakedService object.
     */
    public final function callAction()
    {
        $methodName = $this->_request->getParam('method');
        $this->view->methodName = $methodName;
        $method = $this->_completeObject->getMethod($methodName);
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

        $result = $this->_completeObject->__call((string) $method, $parameters);
        if (is_object($result)) {
            $this->_redirectToObject($result);
        } else {
            $this->view->result = $result;
        }
    }

    /**
     * This method redirects to the view action of a NakedBareEntity or NakedService object.
     * @param NakedObject $no
     */
    protected function _redirectToObject($no)
    {
        if (!($no instanceof NakedObject or $no instanceof NakedEntity)) {
            $factory = $this->_factory->getNakedFactory();
            $no = $factory->create($no);
        }

        if ($no instanceof NakedEntity) {
            if ($no instanceof NakedCompleteEntity) {
                $no = $no->getBareEntity();
            }
            $index = $this->_entityContainer->add($no);
            $type = 'entity';
        } else {
            $index = (string) $no->getClass();
            $type = 'service';
        }
        $params = array(
            'type' => $type,
            'object' => $index
        );
        $this->_helper->Redirector('view', null, null, $params);
    }
}
