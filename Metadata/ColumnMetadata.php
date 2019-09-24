<?php

namespace Arxy\GdprDumpBundle\Metadata;

class ColumnMetadata
{
    /** @var string */
    private $name;

    /** @var string */
    private $transformer;

    /** @var array */
    private $options = [];

    /** @var TableMetadata */
    private $tableMetadata;

    public function __construct(string $name, string $transformer, array $options = [])
    {
        $this->name = $name;
        $this->transformer = $transformer;
        $this->options = $options;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTableMetadata()
    {
        return $this->tableMetadata;
    }

    public function setTableMetadata($tableMetadata)
    {
        $this->tableMetadata = $tableMetadata;
    }

    public function getTransformer()
    {
        return $this->transformer;
    }

    public function getOptions()
    {
        return $this->options;
    }
}
