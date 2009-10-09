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
 * @package    NakedPhp_Form
 */

namespace NakedPhp\Form;
use NakedPhp\Metadata\NakedEntity;
use NakedPhp\Stubs\User;
use NakedPhp\Stubs\Phonenumber;

class StateManagerTest extends \PHPUnit_Framework_TestCase
{
    private $_manager;
    private $_anotherUser;
    private $_anotherUserEntity;
    private $_factory;

    public function setUp()
    {
        $userEntity = new NakedEntity(new User('Snoopy'), null);
        $this->_anotherUserEntity = new NakedEntity($this->_anotherUser = new User('PetitPrince'), null);
        $phonenumberEntity = new NakedEntity(new Phonenumber(), null);
        $iterator = new \ArrayIterator(array(
            10 => $userEntity,
            20 => $this->_anotherUserEntity,
            30 => $phonenumberEntity
        ));
        $this->_manager = new StateManager($iterator);
    }

    public function testPopulatesSelectAccordingToEntitiesAvailable()
    {
        $form = new \Zend_Form();
        $form->addElement('select', 'testingField', array('class' => 'NakedPhp-Stubs-User'));
        $this->_manager->populateOptions($form);
        $options = $form->testingField->getMultiOptions();
        $this->assertEquals(2, count($options));
        $this->assertEquals('User: Snoopy', $options['10']);
        $this->assertEquals('User: PetitPrince', $options['20']);
        return $form;
    }

    /**
     * @depends testPopulatesSelectAccordingToEntitiesAvailable
     */
    public function testSetsAFormStateFromAnEntity(\Zend_Form $form)
    {
        $entity = $this->_getMockEntity();
        $entity->expects($this->once())
               ->method('getState')
               ->will($this->returnValue(array('testingField' => $this->_anotherUser)));
        $this->_manager->setFormState($form, $entity);
    }

    /**
     * @depends testPopulatesSelectAccordingToEntitiesAvailable
     */
    public function testSetsAnObjectStateFromRequest(\Zend_Form $form)
    {
        $entity = $this->_getMockEntity(array('testingField' => $this->_anotherUserEntity));
        $form->populate(array('testingField' => 20));
        $this->_manager->setEntityState($entity, $form);
    }

    public function testIsTransparentToScalarFieldElements()
    {
        $entity = $this->_getMockEntity(array('email' => 'piccoloprincipeazzurro@...'));
        $form = new \Zend_Form();
        $form->addElement('text', 'email');
        $form->populate(array('email' => 'piccoloprincipeazzurro@...'));
        $this->_manager->setEntityState($entity, $form);
    }

    public function testExcludesIgnoredFields()
    {
        $entity = $this->_getMockEntity(array());
        $form = new \Zend_Form();
        $form->addElement('text', 'email', array('ignore' => true));
        $form->populate(array('email' => 'piccoloprincipeazzurro@...'));
        $this->_manager->setEntityState($entity, $form);
    }

    private function _getMockEntity($expectedState = null)
    {
        $mock = $this->getMock('NakedPhp\\Metadata\\NakedEntity', array(), array(), '', false);
        if ($expectedState !== null) {
            $mock->expects($this->once())
                 ->method('setState')
                 ->with($expectedState);
        }
        return $mock;
    }
}