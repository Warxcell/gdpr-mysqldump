<?php

declare(strict_types=1);

namespace Arxy\GdprDumpBundle\Tests;

use Arxy\GdprDumpBundle\ArxyGdprDumpBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array();
        $bundles[] = new \Symfony\Bundle\FrameworkBundle\FrameworkBundle();
        $bundles[] = new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle();
        $bundles[] = new ArxyGdprDumpBundle();

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config.yml');
    }
}