<?php
declare(strict_types=1);

namespace Arxy\GdprDump\DependencyInjection;

use Arxy\GdprDump\Metadata\ColumnMetadata;
use Arxy\GdprDump\Metadata\TableMetadata;
use Arxy\GdprDump\Transformer;
use Arxy\GdprDump\ValueTransformer;
use Ifsnop\Mysqldump\Mysqldump;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class ArxyGdprDumpExtension extends Extension
{
    const TRANSFORMERS_TAG = 'arxy.gdpr_mysqldump.transformer';

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.xml');

        $container->registerForAutoconfiguration(Transformer::class)
            ->addTag(self::TRANSFORMERS_TAG)
            ->setAutowired(true);

        $transformValueDefinition = $container->getDefinition(ValueTransformer::class);
        if (isset($config['value_convertor'])) {
            $transformValueDefinition->setArgument('$valueConvertor', new Reference($config['value_convertor']));
        }

        $mysqldumpDef = $container->getDefinition(Mysqldump::class);
        $mysqldumpDef->setArgument('$dsn', $config['dsn']);
        $mysqldumpDef->setArgument('$dumpSettings', $config['dump_settings']);
        $mysqldumpDef->setArgument('$pdoSettings', $config['pdo_settings']);

        foreach ($config['gdpr'] as $tableName => $tables) {
            $tableMetadataDef = new Definition(TableMetadata::class, [$tableName]);
            $transformValueDefinition->addMethodCall('addTableMetadata', [$tableMetadataDef]);

            foreach ($tables as $colName => $colOptions) {
                $columnMetadataDef = new Definition(
                    ColumnMetadata::class,
                    [$colName, $colOptions['transformer'], $colOptions['options'] ?? []]
                );
                $tableMetadataDef->addMethodCall('addColumn', [$columnMetadataDef]);
            }
        }
    }
}