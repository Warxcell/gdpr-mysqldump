<?php
declare(strict_types=1);

namespace Arxy\GdprDumpBundle\Transformer;

use Arxy\GdprDumpBundle\AbstractTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class SymfonyPasswordTransformer extends AbstractTransformer
{
    /** @var EncoderFactoryInterface */
    private $encoderFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->encoderFactory = $encoderFactory;
        $this->entityManager = $entityManager;
    }

    public function transform($tableName, $colName, $colValue, $row, $options = []): ?string
    {
        foreach ($this->entityManager->getMetadataFactory()->getAllMetadata() as $metadata) {
            /** @var $metadata ClassMetadata */
            if ($tableName === $metadata->getTableName()) {
                $encoder = $this->encoderFactory->getEncoder($metadata->getReflectionClass()->getName());

                $plainPassword = $options['password'];

                $salt = null;

                if (isset($options['saltColumn'])) {
                    $salt = $row[$options['saltColumn']];
                }

                return $encoder->encodePassword($plainPassword, $salt);
            }
        }
        throw new \LogicException('Table '.$tableName.' not found in supported entities');
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setRequired('password');
        $optionsResolver->setDefined('saltColumn');
    }
}

