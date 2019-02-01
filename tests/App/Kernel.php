<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Tests\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use FreezyBee\DataGridBundle\Controller\DataGridController;
use FreezyBee\DataGridBundle\FreezyBeeDataGridBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    use MicroKernelTrait;

    public function registerBundles()
    {
        $bundles = [
            FrameworkBundle::class,
            TwigBundle::class,
            DoctrineBundle::class,
            FreezyBeeDataGridBundle::class,
        ];

        foreach ($bundles as $bundle) {
            yield new $bundle();
        }
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $routes->add('/', AppController::class . '::index');
        $routes->add('/grid/{name}', DataGridController::class . '::handle', 'datagrid_ajax')
            ->addRequirements(['name' => '.*']);
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader): void
    {
        $c->setParameter('kernel.secret', 'x');
        $c->register(AppController::class, AppController::class)
            ->addTag('controller.service_arguments')
            ->setAutowired(true);

        $c->register(BeeGridType::class, BeeGridType::class)
            ->setAutoconfigured(true);

        $c->loadFromExtension('framework', [
            'templating' => [
                'engines' => ['twig'],
            ],
        ]);

        $c->loadFromExtension('twig', [
            'strict_variables' => true,
            'paths' => [__DIR__ . '/templates/'],
        ]);

        $c->loadFromExtension('doctrine', [
            'dbal' => [],
            'orm' => [],
        ]);
    }
}
