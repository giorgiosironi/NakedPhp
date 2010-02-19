<?php

class NakedPhpController extends NakedPhp\Mvc\Controller
{

    public function init()
    {
        $this->_folder = __DIR__ . '/../models/';
        $this->_prefix = 'Example_Model_';
        $this->_serviceClassNames = array('Example_Model_PlaceFactory', 'Example_Model_EventFactory');

        /* Initialize action controller here */
    }

}

