<?php

class NakedPhpController extends NakedPhp\Mvc\Controller
{

    public function init()
    {
        $this->_folder = __DIR__ . '/../models/';
        $this->_prefix = 'Example_Model_';

        /* Initialize action controller here */
    }

}

