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
use NakedPhp\Metadata\NakedField;

class FieldsFormBuilder
{
    /**
     * @param array $fields    NakedField instances
     */
    public function createForm($fields)
    {
        assert('is_array($fields) or $fields instanceof Traversable');
        $form = new \Zend_Form();
        foreach ($fields as $name => $field) {
            $input = $this->createElement($field);
            $input->setAttrib('class', $this->_normalize($field->getType()));
            $form->addElement($input);
        }
        $form->addElement(new \Zend_Form_Element_Submit('nakedphp_submit', array(
                            'value' => 'Edit',
                            'ignore' => 'true'
                         )));
        return $form;
    }

    /**
     * @param NakedField $field     single field
     * @return Zend_Form_Element
     */
    public function createElement(NakedField $field)
    {
        if ($this->_isObjectField($field)) {
            return new \Zend_Form_Element_Select($field->getName());
        } else {
            return new \Zend_Form_Element_Text($field->getName());
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
