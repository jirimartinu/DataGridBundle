<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\DependencyInjection;

use FreezyBee\DataGridBundle\DataGridTypeInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class FreezyBeeDataGridExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(DataGridTypeInterface::class)
            ->setPublic(true)
            ->setAutowired(true);
    }
}
