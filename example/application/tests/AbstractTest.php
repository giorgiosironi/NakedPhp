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
 * @category   Example
 * @package    Example_Test
 */

/**
 * TODO: move in example/tests/controllers
 */
abstract class Example_AbstractTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $_em;

    /**
     * Zend_Session is reset automatically.
     * TODO: Doctrine 2 database
     */
    public function setUp()
    {
        $application = new Zend_Application(
            'testing', 
            APPLICATION_PATH . '/configs/application.ini'
        );
        $this->bootstrap = array($application, 'bootstrap');
        parent::setUp();
        $this->frontController->setParam('bootstrap', $application->getBootstrap());
        $this->frontController->throwExceptions(true);
        $this->_em = $this->frontController->getParam('bootstrap')->getResource('Entitymanagerfactory');
        $this->resetStorage();
    }

    public function resetStorage()
    {
        $classes = $this->_em->getMetadataFactory()->getAllMetadata();
        foreach ($classes as $class) {
            $entities = $this->_em->getRepository($class->name)->findAll();
            foreach ($entities as $entity) {
                $this->_em->remove($entity);
            }
        }
        $this->_em->flush();
        $this->_em->clear();
    }

    public function resetSession()
    {
        $_SESSION = array();
    }

    protected function _createCity($name)
    {
        $this->_newRequest();
        $this->getRequest()
             ->setMethod('POST')
             ->setPost(array(
                 'name' => $name
             ));
        $this->dispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createCity');
    }

    protected function _createPlaceCategory($name)
    {
        $this->_newRequest();
        $this->getRequest()
             ->setMethod('POST')
             ->setPost(array(
                 'name' => $name
             ));
        $this->dispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createPlaceCategory');
    }

    protected function _createPlace()
    {
        $this->_newDispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createPlace');
    }
    
    protected function _newDispatch($url)
    {
        $this->_newRequest();
        $this->dispatch($url);
    }

    protected function _newRequest()
    {
        $this->_em->clear();
        $this->resetRequest()
             ->resetResponse();
    }
}
