<?php
declare(strict_types=1);

namespace Arxy\GdprDumpBundle\Converter;

use Arxy\GdprDumpBundle\ValueConverter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

class DoctrineConverter implements ValueConverter
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function getDoctrineMetadata($tableName)
    {
        foreach ($this->entityManager->getMetadataFactory()->getAllMetadata() as $metadata) {
            /** @var $metadata ClassMetadata */
            if ($tableName === $metadata->getTableName()) {
                return $metadata;
            }
        }
        throw new \LogicException('Table '.$tableName.' not found in supported entities');
    }

    public function convertToPHPValue($tableName, $colName, $colValue)
    {
        try {
            $metadata = $this->getDoctrineMetadata($tableName);

            $type = $metadata->getTypeOfColumn($colName);

            if ($type) {
                return $this->entityManager->getConnection()->convertToPHPValue($colValue, $type);
            } else {
                return $colValue;
            }
        } catch (\Exception $exception) {
            return $colValue;
        }
    }

    public function convertToDatabaseValue($tableName, $colName, $colValue): ?string
    {
        try {
            $metadata = $this->getDoctrineMetadata($tableName);

            $type = $metadata->getTypeOfColumn($colName);

            if ($type) {
                return $this->entityManager->getConnection()->convertToDatabaseValue($colValue, $type);
            } else {
                return $colValue;
            }
        } catch (\Exception $exception) {
            return $colValue;
        }
    }
}
