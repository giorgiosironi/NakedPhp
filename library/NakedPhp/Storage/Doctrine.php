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

class Doctrine
{
    private $_em;

    public function __construct(\Doctrine\ORM\EntityManager $em = null)
    {
        $this->_em = $em;
    }

    public function process(\NakedPhp\Mvc\EntityContainer $container)
    {
        foreach ($container as $key => $entity) {
            $this->_em->persist($entity);
        }
        $this->_em->flush();
    }
}

