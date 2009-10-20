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
use NakedPhp\Metadata\NakedField;
use NakedPhp\Service\MethodCaller;

class FieldsFormBuilder
{
    protected $_caller;

    /**
     * @param MethodCaller $caller
     */
    public function __construct(MethodCaller $caller)
    {
        $this->_caller = $caller;
    }

    /**
     * @param NakedEntity $entity
     * @param array $fields         NakedField instances
     */
    public function createForm(NakedEntity $entity, $fields)
    {
        assert('is_array($fields) or $fields instanceof Traversable');
        $form = new \Zend_Form();
        foreach ($fields as $name => $field) {
            $element = $this->createElement($entity, $field);
            $element->setAttrib('class', $this->_normalize($field->getType()));
            $element->setLabel($name);
            $form->addElement($element);
        }
        $form->addElement(new \Zend_Form_Element_Submit('nakedphp_submit', array(
                            'label' => 'Edit',
                            'ignore' => 'true'
                         )));
        return $form;
    }

    /**
     * @param NakedField $field     single field
     * @return Zend_Form_Element
     */
    public function createElement(NakedEntity $entity, NakedField $field)
    {
        if ($this->_isObjectField($field)) {
            return new \Zend_Form_Element_Select($field->getName());
        } else {
            $methodName = 'choices' . ucfirst($field->getName());
            if ($this->_caller->hasMethod($entity->getClass(), $methodName)) {
                $choices = $this->_caller->call($entity, $methodName); 
                return new \Zend_Form_Element_Select($field->getName());
                $element->setMultiOptions($choices);
            } else {
                return new \Zend_Form_Element_Text($field->getName());
            }
        }
    }

    protected function _isObjectField(NakedField $field)
    {
        return ucfirst($field->getType()) == $field->getType();
    }

    protected function _normalize($className)
    {
        return strtr($className, array('_' => '-', '\\' => '-'));
    }
}
