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

namespace NakedPhp\Mvc\View\Helper;
use NakedPhp\Metadata\NakedObject;

class DisplayObject extends \Zend_View_Helper_Abstract
{
    public function __call($name, $args)
    {
        list ($no, ) = $args;
        $html = '<table>';
        foreach ($no as $fieldName => $value) {
            $html .= "<tr>
            <td>$fieldName</td>
            <td>$value</td>
            </tr>
            ";
        }
        $html .= '</table>';
        return $html;
    }
}
