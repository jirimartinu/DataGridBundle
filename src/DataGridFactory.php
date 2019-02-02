<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle;

use Doctrine\ORM\QueryBuilder;
use FreezyBee\DataGridBundle\DataSource\ArrayDataSource;
use FreezyBee\DataGridBundle\DataSource\DoctrineDataSource;
use FreezyBee\DataGridBundle\Exception\DataGridException;
use FreezyBee\DataGridBundle\Export\DataGridExporterInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class DataGridFactory
{
    /** @var ContainerInterface */
    private $container;

    /** @var EngineInterface */
    private $engine;

    /** @var PropertyAccessorInterface */
    private $propertyAccessor;

    /** @var DataGridExporterInterface */
    private $exporter;

    /**
     * @param ContainerInterface $container
     * @param EngineInterface $engine
     * @param PropertyAccessorInterface $propertyAccessor
     * @param DataGridExporterInterface $exporter
     */
    public function __construct(
        ContainerInterface $container,
        EngineInterface $engine,
        PropertyAccessorInterface $propertyAccessor,
        DataGridExporterInterface $exporter
    ) {
        $this->container = $container;
        $this->engine = $engine;
        $this->propertyAccessor = $propertyAccessor;
        $this->exporter = $exporter;
    }

    /**
     * @param string $className
     * @return DataGrid
     */
    public function create(string $className): DataGrid
    {
        /** @var DataGridTypeInterface $gridType */
        $gridType = $this->container->get($className);

        $builder = new DataGridBuilder();
        $gridType->buildGrid($builder);
        $config = $builder->generateConfig();

        $dataSource = $config->getDataSource();
        if ($dataSource instanceof QueryBuilder) {
            $dataSourceImpl = new DoctrineDataSource($dataSource);
        } elseif (is_array($dataSource)) {
            $dataSourceImpl = new ArrayDataSource($dataSource, $this->propertyAccessor);
        } else {
            throw new DataGridException('Invalid datasource');
        }

        return new DataGrid($this->engine, $this->exporter, $dataSourceImpl, $config, $className);
    }
}
