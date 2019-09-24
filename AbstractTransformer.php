<?php
declare(strict_types=1);

namespace Arxy\GdprDumpBundle;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractTransformer implements Transformer
{
    abstract public function transform($tableName, $colName, $colValue, $row, $options = []): ?string;

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
    }
}
