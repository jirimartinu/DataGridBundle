<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle;

use Doctrine\ORM\QueryBuilder;
use FreezyBee\DataGridBundle\Column\ActionColumn;
use FreezyBee\DataGridBundle\Column\Column;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class DataGridConfig
{
    /** @var QueryBuilder|array|null */
    private $dataSource;

    /** @var Column[] */
    private $columns;

    /** @var ActionColumn */
    private $actionColumn;

    /**
     * @var callable|null
     * callable(mixed $item)
     */
    private $customExportCallback;

    /** @var string|null */
    private $defaultSortColumnName;

    /** @var string|null */
    private $defaultSortColumnDirection;

    /** @var int */
    private $defaultPerPage;

    /** @var string[] */
    private $allowExport;

    /** @var array */
    private $options;

    /**
     * @param array|QueryBuilder|null $dataSource
     * @param Column[] $columns
     * @param ActionColumn $actionColumn
     * @param callable|null $customExportCallback
     * @param string|null $defaultSortColumnName
     * @param string|null $defaultSortColumnDirection
     * @param int $defaultPerPage
     * @param string[] $allowExport
     */
    public function __construct(
        $dataSource,
        array $columns,
        ActionColumn $actionColumn,
        ?callable $customExportCallback,
        ?string $defaultSortColumnName,
        ?string $defaultSortColumnDirection,
        int $defaultPerPage,
        array $allowExport,
        array $options = []
    ) {
        $this->dataSource = $dataSource;
        $this->columns = $columns;
        $this->actionColumn = $actionColumn;
        $this->customExportCallback = $customExportCallback;
        $this->defaultSortColumnName = $defaultSortColumnName;
        $this->defaultSortColumnDirection = $defaultSortColumnDirection;
        $this->defaultPerPage = $defaultPerPage;
        $this->allowExport = $allowExport;
        $this->options = $options;
    }

    /**
     * @return array|QueryBuilder|null
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return Column[]
     */
    public function getRenderedColumns(): array
    {
        return array_filter($this->columns, function (Column $column) {
            return $column->isAllowRender();
        });
    }

    /**
     * @return ActionColumn
     */
    public function getActionColumn(): ActionColumn
    {
        return $this->actionColumn;
    }

    /**
     * @return callable|null
     */
    public function getCustomExportCallback(): ?callable
    {
        return $this->customExportCallback;
    }

    /**
     * @return string|null
     */
    public function getDefaultSortColumnName(): ?string
    {
        return $this->defaultSortColumnName;
    }

    /**
     * @return string|null
     */
    public function getDefaultSortColumnDirection(): ?string
    {
        return $this->defaultSortColumnDirection;
    }

    /**
     * @return int
     */
    public function getDefaultPerPage(): int
    {
        return $this->defaultPerPage;
    }

    /**
     * @return array
     */
    public function getAllowExport(): array
    {
        return $this->allowExport;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
