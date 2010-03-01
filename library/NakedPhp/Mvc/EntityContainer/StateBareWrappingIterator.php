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

namespace NakedPhp\Mvc\EntityContainer;
use NakedPhp\ProgModel\NakedBareObject;
use NakedPhp\MetaModel\NakedFactory;

class StateBareWrappingIterator implements \IteratorAggregate
{
    protected $_objects = null;
    protected $_container;
    protected $_factory;

    public function __construct(UnwrappedContainer $container, NakedFactory $factory)
    {
        $this->_container = $container;
        $this->_factory = $factory;
    }

    /**
     * implements the IteratorAggregate interface
     */
    public function getIterator()
    {
        $this->_lazyWrap();
        return new \ArrayIterator($this->_objects);
    }

    protected function _lazyWrap()
    {
        $this->_objects = array();
        foreach ($this->_container as $key => $entity) {
            $this->_objects[$key] = array(
                'object' => $this->_factory->createBare($entity),
                'state'  => $this->_container->getState($key)
            );
        }
    }
}
