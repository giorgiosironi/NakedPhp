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

class IconLoader extends \Zend_View_Helper_Abstract
{
    /**
     * TODO: try renaming to iconLoader()
     */
    public function __call($name, $args)
    {
        list ($className, ) = $args;
        $html = "li.$className {\nbackground: url(/graphic/icons/$className.png) no-repeat left 50%;\npadding-left: 32px;\n}\n";
        $this->view->headStyle()->appendStyle($html);
    }
}
