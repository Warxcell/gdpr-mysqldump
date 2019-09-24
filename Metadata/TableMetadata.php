<?php

declare(strict_types=1);

namespace Arxy\GdprDump\Metadata;

class TableMetadata
{
    /** @var string */
    private $name;

    /** @var ColumnMetadata[] */
    private $columns = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addColumn(ColumnMetadata $metadata)
    {
        $metadata->setTableMetadata($this);
        $this->columns[$metadata->getName()] = $metadata;
    }

    public function getColumn($colName): ColumnMetadata
    {
        if (!isset($this->columns[$colName])) {
            throw new \InvalidArgumentException(sprintf('Column %s not supported', $colName));
        }

        return $this->columns[$colName];
    }
}
