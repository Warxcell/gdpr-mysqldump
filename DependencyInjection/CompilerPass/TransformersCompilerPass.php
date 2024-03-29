<?php

declare(strict_types=1);

namespace Arxy\GdprDumpBundle\DependencyInjection\CompilerPass;

use Arxy\GdprDumpBundle\DependencyInjection\ArxyGdprDumpExtension;
use Arxy\GdprDumpBundle\ValueTransformer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TransformersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $valueTransformerDefinition = $container->getDefinition(ValueTransformer::class);
        $taggedServices = $container->findTaggedServiceIds(ArxyGdprDumpExtension::TRANSFORMERS_TAG);

        foreach ($taggedServices as $id => $tags) {
            $valueTransformerDefinition->addMethodCall('addTransformer', [new Reference($id), $id]);
        }
    }
}
