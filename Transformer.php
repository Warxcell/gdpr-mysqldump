<?php
declare(strict_types=1);

namespace Arxy\GdprDumpBundle;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface Transformer
{
    public function transform($tableName, $colName, $colValue, $row, $options = []);

    public function configureOptions(OptionsResolver $optionsResolver): void;
}
