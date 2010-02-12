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
 * @package    NakedPhp_ProgModel
 */

namespace NakedPhp\ProgModel;
use NakedPhp\MetaModel\NakedObjectAction;

class PhpAction extends AbstractFacetHolder implements NakedObjectAction
{
    /**
     * @var string
     */
    private $_id;

    /**
     * @var array   PhpActionParameter instances
     */
    private $_params;

    /**
     * @var string
     */
    private $_returnType;

    /**
     * @param string $id
     * @param array $params
     * @param string $returnType
     */
    public function __construct($id = '', array $params = array(), $returnType = 'void')
    {
        $this->_id = $id;
        $this->_params = $params;
        $this->_returnType = $returnType;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getParameters()
    {
        return $this->_params;
    }

    public function getReturnType()
    {
        return $this->_returnType;
    }

    public function __toString()
    {
        return $this->getId();
    }
}
