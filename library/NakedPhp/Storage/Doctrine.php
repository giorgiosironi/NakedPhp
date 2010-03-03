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
use NakedPhp\Mvc\EntityContainer;

class Doctrine
{
    private $_em;

    public function __construct(\Doctrine\ORM\EntityManager $em = null)
    {
        $this->_em = $em;
    }

    public function save(EntityContainer $container)
    {
        $newStates = array();
        foreach ($container as $key => $no) {
            $entity = $no->getObject();
            $state = $container->getState($key);
            switch ($state) {
                case EntityContainer::STATE_NEW:
                    $this->_em->persist($entity);
                    $newStates[$key] = EntityContainer::STATE_DETACHED;
                    break;
                case EntityContainer::STATE_DETACHED:
                    $this->_em->merge($entity);
                    break;
                case EntityContainer::STATE_REMOVED:
                    $entity = $this->_em->merge($entity);
                    $this->_em->remove($entity);
                    $container->delete($key);
                    break;
                default:
                    throw new Exception("State not recognized: $state.");
            }
        }
        try {
            $this->_em->flush();
        } catch (\Exception $e) {
            throw new Exception('Problem detected during flushing ( ' . $e->getMessage() . ').', 0, $e);
        }
        foreach ($newStates as $key => $state) {
            $container->setState($key, $state);
        }
    }
}

