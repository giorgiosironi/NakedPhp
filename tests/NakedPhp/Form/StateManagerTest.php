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
use NakedPhp\ProgModel\NakedBareObject;
use NakedPhp\Stubs\NakedFactoryStub;
use NakedPhp\Stubs\NakedObjectSpecificationStub;
use NakedPhp\Stubs\NakedObjectStub;
use NakedPhp\Stubs\User;
use NakedPhp\Stubs\Phonenumber;

/**
 * TODO: split class. Current responsibilities:
 * 1) translating objects in EntityManager keys and such keys in object
 * 2) setting the state of a NakedObject where applicable
 */
class StateManagerTest extends \PHPUnit_Framework_TestCase
{
    private $_manager;
    private $_anotherUser;
    private $_anotherUserWrapped;
    private $_factory;

    public function setUp()
    {
        $nakedFactory = new NakedFactoryStub();

        $userClass = new NakedObjectSpecificationStub('NakedPhp\Stubs\User');
        $userEntity = new NakedBareObject(new User('Snoopy'), $userClass);
        $this->_anotherUserWrapped = new NakedBareObject($this->_anotherUser = new User('PetitPrince'), $userClass);
        $phonenumberEntity = new NakedObjectStub(new Phonenumber());
        $iterator = new \ArrayIterator(array(
            10 => $userEntity,
            20 => $this->_anotherUserWrapped,
            30 => $phonenumberEntity
        ));

        $this->_manager = new StateManager($nakedFactory, $iterator);
    }

    public function testPopulatesSelectAccordingToEntitiesAvailable()
    {
        $form = new \Zend_Form();
        $select = new \NakedPhp\Form\ObjectSelect('testingField', array('class' => 'NakedPhp-Stubs-User'));
        $form->addElement($select);
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
    public function testSetsAFormStateTranslatingObjectsToKeys(\Zend_Form $form)
    {
        $values = array('testingField' => $this->_anotherUser);
        $this->_manager->setFormState($form, $values);
        $this->assertEquals(20, $form->testingField->getValue());
        // TODO: also with equals() (another object which wrap the same)
    }

    /**
     * @depends testPopulatesSelectAccordingToEntitiesAvailable
     */
    public function testGetsAFormStateTranslatingKeysToObjects(\Zend_Form $form)
    {
        $form->isValid(array('testingField' => 20));

        $values = $this->_manager->getFormState($form);

        $this->assertEquals($values['testingField'], $this->_anotherUserWrapped);
    }

    public function testWhileReturningStateWrapsValuesWhichAreNotRecognizedObjects()
    {
        $form = new \Zend_Form();
        $form->addElement('text', 'email');
        $form->populate(array('email' => 'piccoloprincipeazzurro@...'));

        $values = $this->_manager->getFormState($form);

        $this->assertEquals($values['email'], new NakedObjectStub('piccoloprincipeazzurro@...'));
    }

    public function testExcludesIgnoredFields()
    {
        $form = new \Zend_Form();
        $form->addElement('text', 'email', array('ignore' => true));
        $form->populate(array('email' => 'piccoloprincipeazzurro@...'));
        $this->assertEquals(array(), $this->_manager->getFormState($form));
    }

    private function _getMockEntity($expectedState = null)
    {
        $mock = $this->getMock('NakedPhp\\ProgModel\\NakedBareObject', array(), array(), '', false);
        if ($expectedState !== null) {
            $mock->expects($this->once())
                 ->method('setState')
                 ->with($expectedState);
        }
        return $mock;
    }
}
