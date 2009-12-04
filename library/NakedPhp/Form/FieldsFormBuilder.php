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

class FieldsFormBuilder
{
    /**
     * @param NakedEntity $entity
     * @param array $fields         NakedField instances
     */
    public function createForm(NakedEntity $entity, $fields)
    {
        assert('is_array($fields) or $fields instanceof Traversable');
        $form = new \Zend_Form();
        $class = $entity->getClass();
        $form->setAttrib('class', "nakedphp_entity $class");
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
            $element =  new ObjectSelect($field->getName());
        } else {
            $methodName = 'choices' . ucfirst($field->getName());
            if ($entity->hasHiddenMethod($methodName)) {
                $choices = $entity->__call($methodName); 
                $element = new \Zend_Form_Element_Select($field->getName());
                $element->setMultiOptions($choices);
            } else {
                $element = new \Zend_Form_Element_Text($field->getName());
            }
        }

        $element->clearDecorators();
        $element->addDecorator('ViewHelper')
                ->addDecorator('Errors')
                ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
                ->addDecorator(array('Tooltip' => 'HtmlTag'), array('tag' => 'div'))
                ->addDecorator('HtmlTag', array('tag' => 'dd',
                                                'id'  => $element->getName() . '-element'))
                ->addDecorator('Label', array('tag' => 'dt'));
 

        $methodName = 'disable' . ucfirst($field->getName());
        if ($entity->hasHiddenMethod($methodName)) {
            $disabled = $entity->__call($methodName); 
            if ($disabled) {
                $element->setAttrib('disabled', 'disabled');
                if (is_string($disabled)) {
                    $decorators = $element->getDecorators();
                    $tooltipDecorator = $decorators['Tooltip'];
                    $labelDecorator = $decorators['Zend_Form_Decorator_Label'];
                    $tooltipDecorator->setOption('title', $disabled);
                    $labelDecorator->setOption('title', $disabled);
                }
            }
        }

        $methodName = 'validate' . ucfirst($field->getName());
        if ($entity->hasHiddenMethod($methodName)) {
            $callback = array($entity, $methodName);
            $validator = new \Zend_Validate_Callback($callback);
            $element->addValidator($validator);
        }

        return $element;
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
