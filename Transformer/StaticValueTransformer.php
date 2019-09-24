<?php
declare(strict_types=1);

namespace Arxy\GdprDumpBundle\Transformer;

use Arxy\GdprDumpBundle\AbstractTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StaticValueTransformer extends AbstractTransformer
{
    public function transform($tableName, $colName, $colValue, $row, $options = [])
    {
        return $options['value'];
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setRequired('value');
    }
}
