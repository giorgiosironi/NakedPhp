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
 * @package    NakedPhp_Service
 */

namespace NakedPhp\Service;

class ContextContainer implements \IteratorAggregate, \Countable
{
    private $_urls = array();

    public function remember($url)
    {
        if ($url == $this->getLast()) {
            return false;
        }
        $this->_urls[] = $url;
    }

    public function completed()
    {
        array_pop($this->_urls);
    }

    public function getLast()
    {
        return end($this->_urls);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->_urls);
    }

    public function count()
    {
        return count($this->_urls);
    }
}
