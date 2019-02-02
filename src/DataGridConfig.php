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

    /** @var bool */
    private $allowExport;

    /**
     * @param array|QueryBuilder|null $dataSource
     * @param Column[] $columns
     * @param ActionColumn $actionColumn
     * @param callable|null $customExportCallback
     * @param string|null $defaultSortColumnName
     * @param string|null $defaultSortColumnDirection
     * @param int $defaultPerPage
     * @param bool $allowExport
     */
    public function __construct(
        $dataSource,
        array $columns,
        ActionColumn $actionColumn,
        ?callable $customExportCallback,
        ?string $defaultSortColumnName,
        ?string $defaultSortColumnDirection,
        int $defaultPerPage,
        bool $allowExport
    ) {
        $this->dataSource = $dataSource;
        $this->columns = $columns;
        $this->actionColumn = $actionColumn;
        $this->customExportCallback = $customExportCallback;
        $this->defaultSortColumnName = $defaultSortColumnName;
        $this->defaultSortColumnDirection = $defaultSortColumnDirection;
        $this->defaultPerPage = $defaultPerPage;
        $this->allowExport = $allowExport;
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
     * @return bool
     */
    public function isAllowExport(): bool
    {
        return $this->allowExport;
    }
}
