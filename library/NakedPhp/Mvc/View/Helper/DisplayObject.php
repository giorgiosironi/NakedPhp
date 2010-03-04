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

namespace NakedPhp\Mvc\View\Helper;
use NakedPhp\MetaModel\NakedObject;

class DisplayObject extends \Zend_View_Helper_Abstract
{
    /**
     * TODO: support a string as result of hiddenReason()
     * HACK: traps call such as $this->displayObject($nakedObject);
     *       or they will be treated as a constructor for this class whose name
     *       is DisplayObject
     */
    public function __call($name, $args)
    {
        list ($no, ) = $args;
        if ($collFacet = $no->getFacet('Collection')) {
            $html = "<table class=\"nakedphp_collection TODO_ENTITY_NAME\">";
            foreach ($collFacet->iterator($no) as $entity) {
                $html .= '<tr>';
                foreach ($this->_toArray($entity) as $fieldName => $value) {
                    $html .= "
                    <td class=\"value\">$value</td>
                    ";
                }
                $html .= '</tr>';
            }
            $html .= '</table>';
        } else if ($no instanceof \Traversable) {
            $className = $no->getClassName();
            $html = "<table class=\"nakedphp_entity $className\">";
            foreach ($this->_toArray($no) as $fieldName => $value) {
                $html .= "<tr class=\"$fieldName\">
                <td class=\"fieldName\">$fieldName</td>
                <td class=\"value\">$value</td>
                </tr>
                ";
            }
            $html .= '</table>';
        } else {
            throw new Exception('Object not representable.');
        }
        return $html;
    }

    protected function _toArray(NakedObject $no)
    {
        $array = array();
        foreach ($no as $fieldName => $value) {
            $field = $no->getAssociation($fieldName);
            if ($facet = $field->getFacet('Hidden')) {
                if ($facet->hiddenReason($no)) {
                    continue;
                }
            }

            if (is_object($value) && !method_exists($value, '__toString')) {
                $array[$fieldName] = get_class($value);
            } else {
                $array[$fieldName] = (string) $value;
            }
        }
        return $array;
    }
}
