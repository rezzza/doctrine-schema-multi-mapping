<?php

namespace Rezzza\DoctrineSchemaMultiMapping\Domain;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * MetadataFactory
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class MetadataFactory
{
    private $movingReferences         = array();

    /**
     * Get All metadata edited to allow usage of doctrine schema with multi mapping.
     *
     * @param EntityManager $entityManager entityManager
     *
     * @return ClassMetadata[]
     */
    public function getAllMetadata(EntityManager $entityManager)
    {
        $metadatas  = $entityManager->getMetadataFactory()->getAllMetadata();
        $repository = new MetadataRepository();
        $merger     = new MetadataMerger();

        foreach ($metadatas as $k => $metadata) {
            if ($reference = $repository->findReferenceFor($metadata)) {
                $merger->merge($reference, $metadata);
                $this->movingReferences[$metadata->name] = $reference->name;
                unset($metadatas[$k]);
            } else {
                $repository->addReferenceFor($metadata);
            }
        }

        return $this->resolveMetadatas($metadatas);
    }

    /**
     * When merging metadata, we have to repair definitions
     * of association mappings and discriminator map which
     * refers to replaced metadata.
     *
     * @param array $metadatas metadatas
     */
    private function resolveMetadatas(array $metadatas)
    {
        foreach ($metadatas as $i => $metadata) {
            foreach ($metadata->getAssociationMappings() as $k => $associationMapping) {
                if (array_key_exists($associationMapping['targetEntity'], $this->movingReferences)) {
                    $metadata->associationMapping[$k]['targetEntity'] = $this->movingReferences[$associationMapping['targetEntity']];
                }

                if (array_key_exists($associationMapping['sourceEntity'], $this->movingReferences)) {
                    $metadata->associationMapping[$k]['sourceEntity'] = $this->movingReferences[$associationMapping['sourceEntity']];
                }
            }

            foreach ($metadata->discriminatorMap as $k => $discriminatorMap) {
                if (array_key_exists($discriminatorMap, $this->movingReferences)) {
                    $metadata->discriminatorMap[$k] = $this->movingReferences[$discriminatorMap];
                }
            }

            foreach ($metadata->fieldMappings as $k => $fieldMapping) {
                if (isset($fieldMapping['declared']) && array_key_exists($fieldMapping['declared'], $this->movingReferences)) {
                    $metadata->fieldMappings[$k]['declared'] = $this->movingReferences[$fieldMapping['declared']];
                }

                if (isset($fieldMapping['inherited']) && array_key_exists($fieldMapping['inherited'], $this->movingReferences)) {
                    $metadata->fieldMappings[$k]['inherited'] = $this->movingReferences[$fieldMapping['inherited']];
                }

            }
        }

        return $metadatas;
    }
}
