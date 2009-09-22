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

class StateManager
{
    private $_container;
    private $_normalization = array(
        '_' => '-',
        '\\' => '-'
    );

    public function __construct(\Traversable $entityContainer)
    {
        $this->_container = $entityContainer;    
    }

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
    }

    /**
     * @return array    
     */
    public function getState(\Zend_Form $form)
    {
        $state = array();
        foreach ($form as $name => $element) {
            $value = $element->getValue();
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
        return $state;
    }

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

    protected function _isObjectElement(\Zend_Form_Element $element)
    {
        return $element instanceof \Zend_Form_Element_Multi;
    }

    protected function _isOfNormalizedClassName($object, $normalizedClassName)
    {
        $objectClassName = $object->getClassName();
        return strtr($objectClassName, $this->_normalization) == $normalizedClassName;
    }
}
