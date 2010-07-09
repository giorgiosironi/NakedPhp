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
use NakedPhp\MetaModel\NakedObject;
use NakedPhp\Mvc\EntityContainer;

class Doctrine
{
    // TODO: move on an extracted interface
    const ACTION_NEW = 'new';
    const ACTION_UPDATED = 'updated';
    const ACTION_REMOVED = 'removed';

    private $_em;

    public function __construct(\Doctrine\ORM\EntityManager $em = null)
    {
        $this->_em = $em;
    }

    /**
     * @return array    ACTION_* constants are keys, number of objects
     *                  are values
     */
    public function save(EntityContainer $container)
    {
        $newStates = array();
        $actions = array(self::ACTION_NEW => 0, self::ACTION_UPDATED => 0, self::ACTION_REMOVED => 0);
        foreach ($container as $key => $no) {
            $state = $container->getState($key);
            switch ($state) {
                case EntityContainer::STATE_NEW:
                    $newStates[$key] = EntityContainer::STATE_DETACHED;
                    $actions[self::ACTION_NEW] += $this->_persist($no);
                    break;
                case EntityContainer::STATE_DETACHED:
                    $actions[self::ACTION_UPDATED] += $this->_merge($no);
                    break;
                case EntityContainer::STATE_REMOVED:
                    $actions[self::ACTION_REMOVED] += $this->_remove($no);
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
        return $actions;
    }

    /**
     * @return integer  number of objects affected
     */
    protected function _persist(NakedObject $no)
    {
        $em = $this->_em;
        $callback = function($object) use ($em) {
            $entity = $object->getObject();
            $em->persist($entity);
        };
        return $this->_foreachIfCollection($no, $callback);
    }

    public function merge(EntityContainer $container) {
        foreach ($container as $key => $no) {
            $state = $container->getState($key);
            switch ($state) {
                case EntityContainer::STATE_NEW:
                    break;
                case EntityContainer::STATE_DETACHED:
                case EntityContainer::STATE_REMOVED:
                    $this->_merge($no);
                    break;
                default:
                    throw new Exception("State not recognized: $state.");
            }
        }
    }

    /**
     * @return integer  number of objects affected
     */
    protected function _merge(NakedObject $no)
    {
        if (($collectionFacet = $no->getFacet('Collection')) !== null) {
            $number = 0;
            $newArray = array();
            foreach ($collectionFacet->iterator($no) as $key => $item) {
                $newEntity = $this->_em->merge($item->getObject());
                $newArray[$key] = $newEntity;
                $number++;
            }
            $no->replace($newArray);
        } else {
            $entity = $no->getObject();
            $newEntity = $this->_em->merge($entity);
            $no->replace($newEntity);
            $number = 1;
        }

        return $number;
    }

    /**
     * @return integer  number of objects affected
     */
    protected function _remove(NakedObject $no)
    {
        $em = $this->_em;
        $callback = function($object) use ($em) {
            $entity = $object->getObject();
            //$entity = $em->merge($entity);
            $em->remove($entity);
        };
        return $this->_foreachIfCollection($no, $callback);
    }

    protected function _foreachIfCollection(NakedObject $no, $callback) {
        if (($collectionFacet = $no->getFacet('Collection')) !== null) {
            $number = 0;
            foreach ($collectionFacet->iterator($no) as $item) {
                $callback($item);
                $number++;
            }
        } else {
            $callback($no);
            $number = 1;
        }
        return $number;
    }
}

