<?php

namespace Arxy\GdprDump;

use Arxy\GdprDump\DependencyInjection\CompilerPass\TransformersCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ArxyGdprDumpBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TransformersCompilerPass());
    }
}
