<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class GridTypePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('datagrid.type') as $id => $tag) {
            $container->getDefinition($id)
                ->setPublic(true)
                ->setAutowired(true);
        }
    }
}
