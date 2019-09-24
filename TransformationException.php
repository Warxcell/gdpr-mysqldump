<?php
declare(strict_types=1);

namespace Arxy\GdprDump;

use Arxy\GdprDump\Metadata\ColumnMetadata;

class TransformationException extends \RuntimeException
{
    public static function create(
        Transformer $transformer,
        ColumnMetadata $columnMetadata,
        \Throwable $throwable
    ): \Throwable {
        return new self(
            sprintf(
                'Error while transforming %s.%s with transformer %s',
                $columnMetadata->getTableMetadata()->getName(),
                $columnMetadata->getName(),
                get_class($transformer)
            ), 0, $throwable
        );
    }
}