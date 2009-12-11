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
use NakedPhp\Metadata\NakedObjectAbstract;
use NakedPhp\Metadata\NakedEntity;
use NakedPhp\Metadata\NakedService;

class Controller extends \Zend_Controller_Action
{
    /**
     * @var NakedPhp\Factory    creates nakedphp object
     */
    private $_factory;

    /**
     * @var EntityContainer   contains entity objects
     */
    private $_unwrappedContainer;

    /**
     * @var ContextContainer  remembers current workflow
     */
    private $_contextContainer;

    /**
     * @var NakedPhp\Service\ServiceIterator    lists services
     */
    private $_services;

    /**
     * @var mixed   key to identify service or entity
     */
    private $_objectKey;

    /**
     * @var NakedObjectAbstract     the current object
     */
    private $_completeObject;

    /**
     * @var NakedClass      the class of the current object
     */
    private $_class;

    public final function preDispatch()
    {
        $this->_factory = new \NakedPhp\Factory();
        $this->_nakedFactory = $this->_factory->getNakedFactory();
        $this->_unwrappedContainer = $this->_factory->getUnwrappedContainer();
        $this->_bareWrappingIterator = $this->_factory->getBareWrappingIterator();
        $this->view->session = $this->_bareWrappingIterator;
        $this->view->context = $this->_contextContainer = $this->_factory->getContextContainer();
        $this->view->services = $this->_services = $this->_factory->getServiceIterator();

        $objectKey = $this->_request->getParam('object');
        if ($objectKey !== null) {
            if ($this->_request->getParam('type') == 'service') {
                $provider = $this->_factory->getServiceProvider();
                $object = $provider->getService($objectKey);
                $this->_completeObject = $this->_factory->createCompleteService($object); 
            } else {
                $object = $this->_unwrappedContainer->get($objectKey);
                $bareObject = $this->_nakedFactory->createBare($object);
                $this->_completeObject = $this->_factory->createCompleteEntity($bareObject);
            }
            $this->_objectKey = $objectKey;
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
            $this->renderScript('segments/context.phtml', 'nakedphp_context');
            if ($this->_completeObject) {
                $this->renderScript('segments/methods.phtml', 'nakedphp_methods');
            }
        }
    }

    public final function indexAction()
    {
        $this->_contextContainer->reset();
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
        $this->_contextContainer->remember($this->_helper->Url->url());
        $formBuilder = $this->_factory->getFieldsFormBuilder();
        $form = $formBuilder->createForm($this->_completeObject, $this->_class->getFields());
        $stateManager = $this->_factory->getStateManager()
                                       ->populateOptions($form)
                                       ->setFormState($form, $this->_completeObject);
        if ($this->_request->isPost() && $form->isValidPartial($this->_request->getPost())) {
            $state = $stateManager->setEntityState($this->_completeObject, $form);
            $this->_contextContainer->completed();
            $this->_redirectToObject($this->_completeObject->unwrap());
        } else {
            $this->view->form = $form;
        }
    }

    public final function removeAction()
    {
        $this->_unwrappedContainer->setState($this->_objectKey, EntityContainer::STATE_REMOVED);
    }

    /**
     * This action allows to call a method on a NakedEntity or NakedService object.
     */
    public final function callAction()
    {
        $this->_contextContainer->remember($this->_helper->Url->url());
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
        $this->_contextContainer->completed();
        if (is_object($result)) {
            $this->_redirectToObject($result);
        } else {
            $this->view->result = $result;
        }
    }

    public function saveAction()
    {
        $storage = $this->_factory->getPersistenceStorage();
        $storage->process($this->_unwrappedContainer);
        $this->view->entities = array(
            'new' => array(),
            'detached' => array(),
            'removed' => array()
        );
        /*
        foreach ($this->_unwrappedContainer as $key => $entity) {
            $state = $this->_unwrappedContainer->getState($key);
            $bareEntity = $this->_nakedFactory->create($entity);
            $entityRepresentation = (string) $bareEntity;
            switch ($state) {
                case EntityContainer::STATE_NEW:
                    $storage->persist($entity);
                    $this->_unwrappedContainer->setState($key, EntityContainer::STATE_DETACHED);
                    $this->view->entities['new'][] = $entityRepresentation;
                    break;
                case EntityContainer::STATE_DETACHED:
                    $storage->merge($entity);
                    $this->view->entities['detached'][] = $entityRepresentation;
                    break;
                case EntityContainer::STATE_REMOVED:
                    $storage->merge($entity);
                    $storage->remove($entity);
                    $this->view->entities['removed'][] = $entityRepresentation;
                    break;
                default:
                    throw new Exception("Unknown entity state: $state.");
                    break;
            }
        }
        $storage->flush();
        */
    }

    /**
     * This method redirects to the view action of a NakedEntity or NakedService object.
     * @param object    native object of the Domain Model
     * FIX: 
     */
    protected function _redirectToObject($object)
    {
        $completeObject = $this->_nakedFactory->createBare($object);
        if ($completeObject instanceof NakedEntity) {
            $index = $this->_unwrappedContainer->add($object);
            $type = 'entity';
        } else if ($completeObject instanceof NakedService) {
            $index = (string) $completeObject->getClass();
            $type = 'service';
        } else {
            throw new Exception("Unrecognized object to redirect to.");
        }

        if (count($this->_contextContainer)) {
            return $this->_helper->Redirector->gotoUrl($this->_contextContainer->getLast());
        }

        $params = array(
            'type' => $type,
            'object' => $index
        );
        return $this->_helper->Redirector('view', null, null, $params);
    }
}
