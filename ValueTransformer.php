<?php
declare(strict_types=1);

namespace Arxy\GdprDumpBundle;

use Arxy\GdprDumpBundle\Metadata\ColumnMetadata;
use Arxy\GdprDumpBundle\Metadata\TableMetadata;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValueTransformer
{
    /** @var Transformer[] */
    private $transformers = [];

    /** @var ValueConverter */
    private $valueConverter;

    /** @var OptionsResolver[] */
    private $transformerOptions = [];

    /** @var TableMetadata[] */
    private $tableMetadata = [];

    public function __construct(ValueConverter $valueConverter = null)
    {
        $this->valueConverter = $valueConverter;
    }

    public function addTableMetadata(TableMetadata $tableMetadata): void
    {
        $this->tableMetadata[$tableMetadata->getName()] = $tableMetadata;
    }

    public function addTransformer(Transformer $transformer, $key = null): void
    {
        if ($key === null) {
            $key = get_class($transformer);
        }
        $this->transformers[$key] = $transformer;

        $objectId = spl_object_id($transformer);
        $this->transformerOptions[$objectId] = new OptionsResolver();
        $transformer->configureOptions($this->transformerOptions[$objectId]);
    }

    private function getOptions(ColumnMetadata $columnMetadata, Transformer $transformer): array
    {
        return $this->transformerOptions[spl_object_id($transformer)]->resolve($columnMetadata->getOptions());
    }

    private function getMetadata($tableName, $colName): ColumnMetadata
    {
        if (!isset($this->tableMetadata[$tableName])) {
            throw new \InvalidArgumentException(sprintf('Table %s not supported', $tableName));
        }

        try {
            return $this->tableMetadata[$tableName]->getColumn($colName);
        } catch (\Exception $ex) {
            throw new \InvalidArgumentException(sprintf('Error in table %s', $tableName), 0, $ex);
        }
    }

    private function getTransformer(ColumnMetadata $columnMetadata): Transformer
    {
        if (!isset($this->transformers[$columnMetadata->getTransformer()])) {
            throw new \InvalidArgumentException(
                sprintf('Transformer %s not supported', $columnMetadata->getTransformer())
            );
        }

        return $this->transformers[$columnMetadata->getTransformer()];
    }

    public function transform($tableName, $colName, $colValue, $row): ?string
    {
        try {
            $columnMetadata = $this->getMetadata($tableName, $colName);
        } catch (\Exception $exception) {
            return $colValue;
        }

        $transformer = $this->getTransformer($columnMetadata);

        try {
            $options = $this->getOptions($columnMetadata, $transformer);

            if ($this->valueConverter) {
                $colValue = $this->valueConverter->convertToPHPValue($tableName, $colName, $colValue);
            }

            $colValue = $transformer->transform(
                $tableName,
                $colName,
                $colValue,
                $row,
                $options
            );

            if ($this->valueConverter) {
                $colValue = $this->valueConverter->convertToDatabaseValue($tableName, $colName, $colValue);
            }

            return $colValue;
        } catch (\Exception $ex) {
            throw TransformationException::create($transformer, $columnMetadata, $ex);
        }
    }

    public function __invoke($tableName, $colName, $colValue, $row)
    {
        return $this->transform($tableName, $colName, $colValue, $row);
    }
}
