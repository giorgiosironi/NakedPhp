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
use NakedPhp\Stubs\View;

class IconLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $_headStyleMock;
    private $_helper;

    public function setUp()
    {
        $this->_headStyleMock = $this->getMock('Zend_View_Helper_HeadStyle', array('appendStyle'));
        $view = new View();
        $view->setHelper('headStyle', $this->_headStyleMock);
        $this->_helper = new IconLoader();
        $this->_helper->setView($view);
    }

    public function testGeneratesCssCodeForIconInclusion()
    {
        // TODO: expectation with regular expression
        $code = "li.Example_Class {\nbackground: url(/graphic/icons/Example_Class.png) no-repeat left 50%;\npadding-left: 32px;\n}\n";
        $this->_headStyleMock->expects($this->once())
                             ->method('appendStyle')
                             ->with($code);
        $this->_helper->iconLoader('Example_Class');
    }

    public function testBaseUrlIsRecognized()
    {
        $this->markTestIncomplete();
    }
}
