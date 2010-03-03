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

class StateBareIterator implements \IteratorAggregate
{
    protected $_container;

    public function __construct(BareContainer $container)
    {
        $this->_container = $container;
    }

    /**
     * implements the IteratorAggregate interface
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_load());
    }

    protected function _load()
    {
        $objects = array();
        foreach ($this->_container as $key => $entity) {
            $objects[$key] = array(
                'object' => $entity,
                'state'  => $this->_container->getState($key)
            );
        }
        return $objects;
    }
}
