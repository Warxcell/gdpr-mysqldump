<?php
declare(strict_types=1);

namespace Arxy\GdprDump\Transformer;

use Arxy\GdprDump\AbstractTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StaticValueTransformer extends AbstractTransformer
{
    public function transform($tableName, $colName, $colValue, $row, $options = []): ?string
    {
        return $options['value'];
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setRequired('value');
    }
}
