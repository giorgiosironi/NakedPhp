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

namespace NakedPhp\Mvc;

class Controller extends \Zend_Controller_Action
{
    private $_session;

    public final function preDispatch()
    {
        //\Zend_Session::start();
        $this->_session = new \Zend_Session_Namespace('NakedPhp');
        $name = 'name' . rand();
        $this->_session->$name = uniqid("Prova");
        $this->view->session = $this->_session;
    }

    public final function postDispatch()
    {
        $paths = $this->view->getScriptPaths();
        $originalPath = array_shift($paths);
        $this->view->setScriptPath($paths);
        $this->view->addScriptPath(__DIR__ . '/views/scripts/');
        $this->view->addHelperPath(realpath(__DIR__) . '/View/Helper', 'NakedPhp\\Mvc\\View\\Helper\\');

        if (!$this->_helper->ViewRenderer->getNoRender()) {
            $this->render(null, null, true);
            $this->renderScript('segments/session.phtml', 'nakedphp_session');
        }
    }

    public final function indexAction()
    {
        echo "Hello world from NakedPhp!";
        $collection = new \Doctrine\Common\Collections\Collection();
    }
}
