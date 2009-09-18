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
use NakedPhp\Metadata\NakedMethod;

class MethodFormBuilder
{
    public function createForm(NakedMethod $method)
    {
        $form = new \Zend_Form();
        foreach ($method->getParams() as $name => $param) {
            $input = new \Zend_Form_Element_Text($param->getName());
            $form->addElement($input);
        }
        $form->addElement(new \Zend_Form_Element_Submit('nakedphp_submit', array(
                            'value' => 'Call',
                            'ignore' => 'true'
                         )));
        return $form;
    }
}
