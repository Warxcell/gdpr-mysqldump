<?php
declare(strict_types=1);

namespace Arxy\GdprDumpBundle;

use Arxy\GdprDumpBundle\DependencyInjection\CompilerPass\TransformersCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ArxyGdprDumpBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TransformersCompilerPass());
    }
}
