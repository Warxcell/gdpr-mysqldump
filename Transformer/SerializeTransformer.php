<?php


namespace Arxy\GdprDump\Transformer;


use Arxy\GdprDump\AbstractTransformer;
use Arxy\GdprDump\Transformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SerializeTransformer extends AbstractTransformer
{
    /** @var Transformer */
    private $originalTransformer;

    public function __construct(Transformer $originalTransformer)
    {
        $this->originalTransformer = $originalTransformer;
    }

    public function transform($tableName, $colName, $colValue, $row, $options = [])
    {
        return serialize($this->originalTransformer->transform($tableName, $colName, $colValue, $row, $options));
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $this->originalTransformer->configureOptions($optionsResolver);
    }
}
