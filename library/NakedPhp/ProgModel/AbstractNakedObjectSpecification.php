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
use NakedPhp\MetaModel\NakedObjectSpecification;

/**
 * Wraps properties about a domain class.
 */
abstract class AbstractNakedObjectSpecification extends AbstractFacetHolder implements NakedObjectSpecification
{
    /**
     * @var string
     */
    protected $_className;

    /**
     * @var array of NakedObjectAction instances
     */
    protected $_methods;

    /**
     * @param string $className
     * @param array $methods        NakedObjectAction instances; keys are method names
     */
    public function __construct($className = '', array $methods = array())
    {
        $this->_className = $className;
        $this->_methods = $methods;
    }

    /**
     * @return array NakedObjectAction
     */
    public function getObjectActions()
    {
        return $this->_methods;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectAction($name)
    {
        $methods = $this->getObjectActions();
        return $methods[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasMethod($name)
    {
        $methods = $this->getObjectActions();
        return isset($methods[$name]);
    }

    /**
     * @return string   the fully qualified class name
     */
    public function getClassName()
    {
        return $this->_className;
    }

    public function __toString()
    {
        return $this->getClassName();
    }
}

