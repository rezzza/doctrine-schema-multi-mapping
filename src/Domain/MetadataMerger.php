<?php

namespace Rezzza\DoctrineSchemaMultiMapping\Domain;

use Doctrine\ORM\Mapping\ClassMetadata;

class MetadataMerger
{
    public function merge(ClassMetadata $reference, ClassMetadata $redundancy)
    {
        $notSupported = array(
            'customGeneratorDefinition',
            'customRepositoryClassName',
            'identifier',
            'inheritanceType',
            'generatorType',
            'isIdentifierComposite',
            'containsForeignIdentifier',
            'idGenerator',
            'sequenceGeneratorDefinition',
            'tableGeneratorDefinition',
            'changeTrackingPolicy',
        );

        foreach ($notSupported as $property) {
            if ($reference->$property != $redundancy->$property) {
                throw new Exception\UnsupportedException(sprintf('"%s" mapping property changed on table "%s", not supported', $property, $redundancy->getTableName()));
            }
        }

        $this->guardFieldMappingsConsistency($reference, $redundancy);
        $reference->fieldMappings       = array_merge($reference->fieldMappings, $redundancy->fieldMappings);

        $this->guardFieldNamesConsistency($reference, $redundancy);
        $reference->fieldNames          = array_merge($reference->fieldNames, $redundancy->fieldNames);

        // @todo check for validating mergeability of association mappings.
        $reference->associationMappings = array_merge($reference->associationMappings, $redundancy->associationMappings);
    }

    private function guardFieldMappingsConsistency(ClassMetadata $reference, ClassMetadata $redundancy)
    {
        $referenceMappings  = $reference->fieldMappings;
        $redundancyMappings = $redundancy->fieldMappings;

        foreach (array_intersect_key($referenceMappings, $redundancyMappings) as $key => $val) {
            // theses Mappings have to be removed since references will change.
            unset($referenceMappings[$key]['declared'], $referenceMappings[$key]['inherited'], $referenceMappings[$key]['originalClass']);
            unset($redundancyMappings[$key]['declared'], $redundancyMappings[$key]['inherited'], $redundancyMappings[$key]['originalClass']);

            if ($referenceMappings[$key] != $redundancyMappings[$key]) {
                throw new Exception\LogicException(sprintf('Field mapping "%s" changed on table "%s"', $key, $redundancy->getTableName()));
            }
        }
    }

    private function guardFieldNamesConsistency(ClassMetadata $reference, ClassMetadata $redundancy)
    {
        $referenceFields  = $reference->fieldNames;
        $redundancyFields = $redundancy->fieldNames;

        foreach (array_intersect_key($referenceFields, $redundancyFields) as $key => $val) {
            if ($referenceFields[$key] != $redundancyFields[$key]) {
                throw new Exception\LogicException(sprintf('Field name "%s" changed on table "%s"', $key, $redundancy->getTableName()));
            }
        }
    }
}
