<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\ORM\Persisters;

use Doctrine\Common\DoctrineException;

/**
 * The joined subclass persister maps a single entity instance to several tables in the
 * database as it is defined by <tt>Class Table Inheritance</tt>.
 *
 * @author      Roman Borschel <roman@code-factory.org>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version     $Revision$
 * @link        www.doctrine-project.org
 * @since       2.0
 */
class JoinedSubclassPersister extends StandardEntityPersister
{
    /** Map that maps column names to the table names that own them.
     *  This is mainly a temporary cache, used during a single request.
     */
    private $_owningTableMap = array();

    /**
     * {@inheritdoc}
     *
     * @override
     */
    protected function _prepareData($entity, array &$result, $isInsert = false)
    {
        parent::_prepareData($entity, $result, $isInsert);
        // Populate the discriminator column
        if ($isInsert) {
            $discColumn = $this->_class->discriminatorColumn;
            $rootClass = $this->_em->getClassMetadata($this->_class->rootEntityName);
            $result[$rootClass->primaryTable['name']][$discColumn['name']] =
            $this->_class->discriminatorValue;
        }
    }

    /**
     * This function finds the ClassMetadata instance in a inheritance hierarchy
     * that is responsible for enabling versioning.
     *
     * @return mixed $versionedClass  ClassMetadata instance or false if versioning is not enabled
     */
    private function _getVersionedClassMetadata()
    {
        if ($isVersioned = $this->_class->isVersioned) {
            if (isset($this->_class->fieldMappings[$this->_class->versionField]['inherited'])) {
                $definingClassName = $this->_class->fieldMappings[$this->_class->versionField]['inherited'];
                $versionedClass = $this->_em->getClassMetadata($definingClassName);
            } else {
                $versionedClass = $this->_class;
            }
            return $versionedClass;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @override
     */
    public function getOwningTable($fieldName)
    {
        if ( ! isset($this->_owningTableMap[$fieldName])) {
            if (isset($this->_class->associationMappings[$fieldName])) {
                if (isset($this->_class->inheritedAssociationFields[$fieldName])) {
                    $this->_owningTableMap[$fieldName] = $this->_em->getClassMetadata(
                    $this->_class->inheritedAssociationFields[$fieldName])->primaryTable['name'];
                } else {
                    $this->_owningTableMap[$fieldName] = $this->_class->primaryTable['name'];
                }
            } else if (isset($this->_class->fieldMappings[$fieldName]['inherited'])) {
                $this->_owningTableMap[$fieldName] = $this->_em->getClassMetadata(
                $this->_class->fieldMappings[$fieldName]['inherited'])->primaryTable['name'];
            } else {
                $this->_owningTableMap[$fieldName] = $this->_class->primaryTable['name'];
            }
        }
        return $this->_owningTableMap[$fieldName];
    }

    /**
     * {@inheritdoc}
     *
     * @override
     */
    public function executeInserts()
    {
        if ( ! $this->_queuedInserts) {
            return;
        }

        if ($isVersioned = $this->_class->isVersioned) {
            $versionedClass = $this->_getVersionedClassMetadata();
        }

        $postInsertIds = array();
        $idGen = $this->_class->idGenerator;
        $isPostInsertId = $idGen->isPostInsertGenerator();
        $sqlLogger = $this->_conn->getConfiguration()->getSqlLogger();

        // Prepare statements for all tables
        $stmts = $classes = array();
        $stmts[$this->_class->primaryTable['name']] = $this->_conn->prepare($this->_class->insertSql);
        $sql[$this->_class->primaryTable['name']] = $this->_class->insertSql;
        foreach ($this->_class->parentClasses as $parentClass) {
            $parentClass = $this->_em->getClassMetadata($parentClass);
            $sql[$parentClass->primaryTable['name']] = $parentClass->insertSql;
            $stmts[$parentClass->primaryTable['name']] = $this->_conn->prepare($parentClass->insertSql);
        }
        $rootTableName = $this->_em->getClassMetadata($this->_class->rootEntityName)->primaryTable['name'];

        foreach ($this->_queuedInserts as $entity) {
            $insertData = array();
            $this->_prepareData($entity, $insertData, true);

            // Execute insert on root table
            $stmt = $stmts[$rootTableName];
            $paramIndex = 1;
            if ($sqlLogger) {
                $params = array();
                foreach ($insertData[$rootTableName] as $columnName => $value) {
                    $params[$paramIndex] = $value;
                    $stmt->bindValue($paramIndex++, $value);
                }
                $sqlLogger->logSql($sql[$rootTableName], $params);
            } else {
                foreach ($insertData[$rootTableName] as $columnName => $value) {
                    $stmt->bindValue($paramIndex++, $value);
                }
            }
            $stmt->execute();
            unset($insertData[$rootTableName]);

            if ($isPostInsertId) {
                $id = $idGen->generate($this->_em, $entity);
                $postInsertIds[$id] = $entity;
            } else {
                $id = $this->_em->getUnitOfWork()->getEntityIdentifier($entity);
            }

            // Execute inserts on subtables
            foreach ($insertData as $tableName => $data) {
                $stmt = $stmts[$tableName];
                $paramIndex = 1;
                if ($sqlLogger) {
                    $params = array();
                    foreach ((array) $id as $idVal) {
                        $params[$paramIndex] = $idVal;
                        $stmt->bindValue($paramIndex++, $idVal);
                    }
                    foreach ($data as $columnName => $value) {
                        $params[$paramIndex] = $value;
                        $stmt->bindValue($paramIndex++, $value);
                    }
                    $sqlLogger->logSql($sql[$tableName], $params);
                } else {
                    foreach ((array) $id as $idVal) {
                        $stmt->bindValue($paramIndex++, $idVal);
                    }
                    foreach ($data as $columnName => $value) {
                        $stmt->bindValue($paramIndex++, $value);
                    }
                }
                $stmt->execute();
            }
        }

        foreach ($stmts as $stmt) {
            $stmt->closeCursor();
        }

        if ($isVersioned) {
            $this->_assignDefaultVersionValue($versionedClass, $entity, $id);
        }

        $this->_queuedInserts = array();

        return $postInsertIds;
    }

    /**
     * Updates an entity.
     *
     * @param object $entity The entity to update.
     * @override
     */
    public function update($entity)
    {
        $updateData = array();
        $this->_prepareData($entity, $updateData);

        $id = array_combine(
            $this->_class->getIdentifierColumnNames(),
            $this->_em->getUnitOfWork()->getEntityIdentifier($entity)
        );

        if ($isVersioned = $this->_class->isVersioned) {
            $versionedClass = $this->_getVersionedClassMetadata();
            $versionedTable = $versionedClass->primaryTable['name'];
        }

        if ($updateData) {
            foreach ($updateData as $tableName => $data) {
                if ($isVersioned && $versionedTable == $tableName) {
                    $this->_doUpdate($entity, $tableName, $data, $id);
                } else {
                    $this->_conn->update($tableName, $data, $id);
                }
            }
            if ($isVersioned && ! isset($updateData[$versionedTable])) {
                $this->_doUpdate($entity, $versionedTable, array(), $id);
            }
        }
    }

    /**
     * Deletes an entity.
     *
     * @param object $entity The entity to delete.
     * @override
     */
    public function delete($entity)
    {
        $id = array_combine(
            $this->_class->identifier,
            $this->_em->getUnitOfWork()->getEntityIdentifier($entity)
        );

        // If the database platform supports FKs, just
        // delete the row from the root table. Cascades do the rest.
        if ($this->_conn->getDatabasePlatform()->supportsForeignKeyConstraints()) {
            $this->_conn->delete($this->_em->getClassMetadata($this->_class->rootEntityName)
                    ->primaryTable['name'], $id);
        } else {
            // Delete from all tables individually, starting from this class' table up to the root table.
            $this->_conn->delete($this->_class->primaryTable['name'], $id);
            foreach ($this->_class->parentClasses as $parentClass) {
                $this->_conn->delete($this->_em->getClassMetadata($parentClass)->primaryTable['name'], $id);
            }
        }
    }

    /**
     * Gets the SELECT SQL to select one or more entities by a set of field criteria.
     *
     * @param array $criteria
     * @return string The SQL.
     * @override
     */
    protected function _getSelectEntitiesSql(array &$criteria, $assoc = null)
    {
        $tableAliases = array();
        $aliasIndex = 1;
        $idColumns = $this->_class->getIdentifierColumnNames();
        $baseTableAlias = 't0';
            
        foreach (array_merge($this->_class->subClasses, $this->_class->parentClasses) as $className) {
            $tableAliases[$className] = 't' . $aliasIndex++;
        }

        // Add regular columns
        $columnList = '';
        foreach ($this->_class->fieldMappings as $fieldName => $mapping) {
            $tableAlias = isset($mapping['inherited']) ?
                    $tableAliases[$mapping['inherited']] : $baseTableAlias;
            if ($columnList != '') $columnList .= ', ';
            $columnList .= $tableAlias . '.' . $this->_class->getQuotedColumnName($fieldName, $this->_platform);
        }
        
        // Add foreign key columns
        foreach ($this->_class->associationMappings as $assoc2) {
            if ($assoc2->isOwningSide && $assoc2->isOneToOne()) {
                foreach ($assoc2->targetToSourceKeyColumns as $srcColumn) {
                    $columnList .= ', ' . $assoc2->getQuotedJoinColumnName($srcColumn, $this->_platform);
                }
            }
        }
        
        // Add discriminator column
        if ($this->_class->rootEntityName == $this->_class->name) {
            $columnList .= ', ' . $baseTableAlias . '.' .
                    $this->_class->getQuotedDiscriminatorColumnName($this->_platform);
        } else {
            $columnList .= ', ' . $tableAliases[$this->_class->rootEntityName] . '.' .
                    $this->_class->getQuotedDiscriminatorColumnName($this->_platform);
        }

        // INNER JOIN parent tables
        $joinSql = '';
        foreach ($this->_class->parentClasses as $parentClassName) {
            $parentClass = $this->_em->getClassMetadata($parentClassName);
            $tableAlias = $tableAliases[$parentClassName];
            $joinSql .= ' INNER JOIN ' . $parentClass->getQuotedTableName($this->_platform) . ' ' . $tableAlias . ' ON ';
            $first = true;
            foreach ($idColumns as $idColumn) {
                if ($first) $first = false; else $joinSql .= ' AND ';
                $joinSql .= $baseTableAlias . '.' . $idColumn . ' = ' . $tableAlias . '.' . $idColumn;
            }
        }

        // OUTER JOIN sub tables
        foreach ($this->_class->subClasses as $subClassName) {
            $subClass = $this->_em->getClassMetadata($subClassName);
            $tableAlias = $tableAliases[$subClassName];

            // Add subclass columns
            foreach ($subClass->fieldMappings as $fieldName => $mapping) {
                if (isset($mapping['inherited'])) {
                    continue;
                }
                $columnList .= ', ' . $tableAlias . '.' . $subClass->getQuotedColumnName($fieldName, $this->_platform);
            }
            
            // Add join columns (foreign keys)
            foreach ($subClass->associationMappings as $assoc2) {
                if ($assoc2->isOwningSide && $assoc2->isOneToOne() && ! isset($subClass->inheritedAssociationFields[$assoc2->sourceFieldName])) {
                    foreach ($assoc2->targetToSourceKeyColumns as $srcColumn) {
                        $columnList .= ', ' . $tableAlias . '.' . $assoc2->getQuotedJoinColumnName($srcColumn, $this->_platform);
                    }
                }
            }
            
            // Add LEFT JOIN
            $joinSql .= ' LEFT JOIN ' . $subClass->getQuotedTableName($this->_platform) . ' ' . $tableAlias . ' ON ';
            $first = true;
            foreach ($idColumns as $idColumn) {
                if ($first) $first = false; else $joinSql .= ' AND ';
                $joinSql .= $baseTableAlias . '.' . $idColumn . ' = ' . $tableAlias . '.' . $idColumn;
            }
        }

        $conditionSql = '';
        foreach ($criteria as $field => $value) {
            if ($conditionSql != '') $conditionSql .= ' AND ';
            $conditionSql .= $baseTableAlias . '.';
            if (isset($this->_class->columnNames[$field])) {
                $conditionSql .= $this->_class->getQuotedColumnName($field, $this->_platform);
            } else if ($assoc !== null) {
                $conditionSql .= $assoc->getQuotedJoinColumnName($field, $this->_platform);
            } else {
                throw DoctrineException::unrecognizedField($field);
            }
            $conditionSql .= ' = ?';
        }

        return 'SELECT ' . $columnList
                . ' FROM ' . $this->_class->getQuotedTableName($this->_platform) . ' ' . $baseTableAlias
                . $joinSql
                . ($conditionSql != '' ? ' WHERE ' . $conditionSql : '');
    }
}