<?php

namespace Rezzza\DoctrineSchemaMultiMapping\Domain;

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * MetadataRepository
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class MetadataRepository
{
    /**
     * @var array
     */
    private $references = array();

    /**
     * @param ClassMetadata $metadata metadata
     *
     * @return ClassMetadata|null
     */
    public function findReferenceFor(ClassMetadata $metadata)
    {
        $table      = $metadata->getTableName();
        $references = $this->getReferencesForTable($table);

        if (empty($references)) {
            return;
        }

        if ($metadata->isInheritanceTypeNone()) {
            if (count($references) > 1) {
                throw new Exception\UnsupportedException(sprintf('You use different inheritance type on table %s', $table));
            }

            return current($references);
        }

        if (false === $metadata->isInheritanceTypeSingleTable()) {
            throw new Exception\UnsupportedException('Only “single_table“ inheritance type is supported by Rezzza\DoctrineSchemaMultiMapping at this moment.');
        }

        $discriminatorValue  = $metadata->discriminatorValue;
        $discriminatorColumn = $metadata->discriminatorColumn;

        foreach ($references as $reference) {
            if ($discriminatorColumn != $reference->discriminatorColumn) {
                throw new \LogicException(sprintf('You have 2 tables with inherhitance Single Table with a different discriminatorColumn on table "%s"', $table));
            }

            if ($discriminatorValue == $reference->discriminatorValue) {
                return $reference;
            }
        }
    }

    /**
     * @param ClassMetadata $metadata metadata
     */
    public function addReferenceFor(ClassMetadata $metadata)
    {
        $this->references[$metadata->getTableName()][] = $metadata;
    }

    /**
     * @param string $table table
     *
     * @return ClassMetadata[]
     */
    private function getReferencesForTable($table)
    {
        return array_key_exists($table, $this->references) ? $this->references[$table] : array();
    }
}
