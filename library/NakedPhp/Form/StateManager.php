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

class StateManager
{
    private $_container;
    private $_normalization = array(
        '_' => '-',
        '\\' => '-'
    );

    /**
     * @param Traversable $entityContainer  a container of NakedEntity instances
     */
    public function __construct(\Traversable $entityContainer)
    {
        $this->_container = $entityContainer;    
    }

    /**
     * @param Zend_Form $form   form with select to populate with options from
     *                          the container
     * @return StateManager     provides a fluent interface
     */
    public function populateOptions(\Zend_Form $form)
    {
        foreach ($this->_getObjectElements($form) as $name => $element) {
            $className = $element->getAttrib('class');
            foreach ($this->_container as $key => $object) {
                if ($this->_isOfNormalizedClassName($object, $className)) {
                    $element->addMultiOption($key, (string) $object);
                }
            }
        }

        return $this;
    }

    /**
     * @param NakedEntity $entity
     * @param Zend_Form $form   form to get values from
     * @return StateManager     provides a fluent interface
     */
    public function setEntityState(NakedEntity $entity, \Zend_Form $form)
    {
        $state = array();
        foreach ($form->getValues() as $name => $value) {
            $element = $form->$name;
            if ($this->_isObjectElement($element)) {
                foreach ($this->_container as $key => $object) {
                    if ($key == $value) {
                        $state[$name] = $object;
                    }
                }
            } else {
                $state[$name] = $value;
            }
        }
        $entity->setState($state);

        return $this;
    }

    /**
     * @param Zend_Form $form
     * @param NakedEntity $entity
     * @return StateManager     provides a fluent interface
     */
    public function setFormState(\Zend_Form $form, NakedEntity $entity)
    {
        $state = $entity->getState();
        foreach ($form as $name => $element) {
            if ($this->_isObjectElement($element)) {
                foreach ($this->_container as $key => $object) {
                    if ($object->isWrapping($state[$name])) {
                        $state[$name] = $key;
                        break;
                    }
                }
            }
        }
        $form->populate($state);

        return $this;
    }

    /**
     * @return array
     */
    protected function _getObjectElements(\Zend_Form $form)
    {
        $elements = array();
        foreach ($form as $name => $element) {
            if ($this->_isObjectElement($element)) {
                $elements[$name] = $element;
            }
        }
        return $elements;
    }

    /**
     * @return boolean
     */
    protected function _isObjectElement(\Zend_Form_Element $element)
    {
        return $element instanceof ObjectSelect;
    }

    /**
     * @param NakedEntity
     * @param string
     * @return boolean
     */
    protected function _isOfNormalizedClassName(NakedEntity $object, $normalizedClassName)
    {
        $objectClassName = $object->getClassName();
        return strtr($objectClassName, $this->_normalization) == $normalizedClassName;
    }
}
