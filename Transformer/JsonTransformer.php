<?php
declare(strict_types=1);

namespace Arxy\GdprDumpBundle\Transformer;

use Arxy\GdprDumpBundle\AbstractTransformer;
use Arxy\GdprDumpBundle\Transformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JsonTransformer extends AbstractTransformer
{
    /** @var Transformer */
    private $originalTransformer;

    public function __construct(Transformer $originalTransformer)
    {
        $this->originalTransformer = $originalTransformer;
    }

    public function transform($tableName, $colName, $colValue, $row, $options = [])
    {
        return json_encode($this->originalTransformer->transform($tableName, $colName, $colValue, $row, $options));
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $this->originalTransformer->configureOptions($optionsResolver);
    }
}
