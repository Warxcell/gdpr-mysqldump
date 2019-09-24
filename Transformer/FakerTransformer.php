<?php
declare(strict_types=1);

namespace Arxy\GdprDumpBundle\Transformer;

use Arxy\GdprDumpBundle\AbstractTransformer;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FakerTransformer extends AbstractTransformer
{
    /** @var Generator */
    private $defaultGenerator;

    /** @var Generator[] */
    private $generators = [];

    public function __construct(Generator $generator = null)
    {
        if ($generator === null) {
            $generator = Factory::create();
        }
        $this->defaultGenerator = $generator;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setRequired('generator');
        $optionsResolver->setDefault('locale', null);
        $optionsResolver->setDefault('arguments', []);
    }

    private function getGenerator($locale = null)
    {
        if ($locale === null) {
            return $this->defaultGenerator;
        } else {
            if (!isset($this->generators[$locale])) {
                $this->generators[$locale] = Factory::create($locale);
            }

            return $this->generators[$locale];
        }
    }

    public function transform($tableName, $colName, $colValue, $row, $options = []): ?string
    {
        return $this->getGenerator($options['locale'])->format($options['generator'], $options['arguments']);
    }
}
