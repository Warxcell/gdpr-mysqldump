<?php
declare(strict_types=1);

namespace Arxy\GdprDump;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface Transformer
{
    public function transform($tableName, $colName, $colValue, $row, $options = []): ?string;

    public function configureOptions(OptionsResolver $optionsResolver): void;
}
