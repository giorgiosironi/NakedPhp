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
 * @package    NakedPhp_Storage
 */

namespace NakedPhp\Storage;

class AbstractFactoryAndRepository
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $_em;

    /**
     * @var string  the name of the managed @see NakedEntitySpecification
     */
    protected $_entityClassName;
    
    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->_em = $em;
    }

    /**
     * Creates a new, transient entity.
     * @return object
     */
    public function createNew()
    {
        $className = $this->_entityClassName;
        return new $className;
    }

    public function find($identifier)
    {
        return $this->_em->find($this->_entityClassName, $identifier);
    }

    public function findAll()
    {
        return $this->_em->getRepository($this->_entityClassName)->findAll();
    }
}
