<?php

class Example_Layout extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $this->getBootstrap()->bootstrap('View');
        $view = $this->getBootstrap()->getResource('View');
        Zend_Layout::startMvc($this->getOptions());
        return Zend_Layout::getMvcInstance();
    }
}
