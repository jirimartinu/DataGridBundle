<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Column;

use FreezyBee\DataGridBundle\Filter\DateRangeFilter;
use FreezyBee\DataGridBundle\Filter\Filter;
use FreezyBee\DataGridBundle\Filter\NumberRangeFilter;
use FreezyBee\DataGridBundle\Filter\SelectEntityFilter;
use FreezyBee\DataGridBundle\Filter\SelectFilter;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
abstract class Column
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $label;

    /** @var string */
    protected $contentColumnName;

    /** @var string[] */
    protected $filterColumnNames;

    /** @var string[] */
    protected $sortColumnNames;

    /** @var Filter|null */
    protected $filter;

    /** @var bool */
    protected $filterable = false;

    /** @var bool */
    protected $sortable = false;

    /** @var string|null */
    protected $customTemplate;

    /**
     * @var callable|null
     * callable(mixed $value)
     */
    protected $customRendererCallback;

    /**
     * @var callable|null
     * callable(QueryBuilder $qb, mixed $value)
     */
    protected $customFilterCallback;

    /**
     * @var callable|null
     * callable(QueryBuilder $qb, string $direction)
     */
    protected $customSortCallback;

    /**
     * @param string $name
     * @param string $label
     * @param string|null $contentColumnName
     */
    public function __construct(string $name, string $label, ?string $contentColumnName = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->contentColumnName = $contentColumnName ?? $name;
        $this->filterColumnNames = [$this->contentColumnName];
        $this->sortColumnNames = [$this->contentColumnName];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getContentColumnName(): string
    {
        return $this->contentColumnName;
    }

    /**
     * @return string[]
     */
    public function getFilterColumnNames(): array
    {
        return $this->filterColumnNames;
    }

    /**
     * @return string[]
     */
    public function getSortColumnNames(): array
    {
        return $this->sortColumnNames;
    }

    /**
     * @return Filter|null
     */
    public function getFilter(): ?Filter
    {
        return $this->filter;
    }

    /**
     * @param array $options
     * @return Column
     */
    public function setSelectFilter(array $options): self
    {
        $this->filter = new SelectFilter($options);
        return $this->setFilterable();
    }

    /**
     * @return Column
     */
    public function setSelectBooleanFilter(): self
    {
        $this->filter = new SelectFilter(['Ano' => 1, 'Ne' => 0]);
        return $this->setFilterable();
    }

    /**
     * @return Column
     */
    public function setDateRangeFilter(): self
    {
        $this->filter = new DateRangeFilter();
        return $this->setFilterable();
    }

    /**
     * @return Column
     */
    public function setNumberRangeFilter(): self
    {
        $this->filter = new NumberRangeFilter();
        return $this->setFilterable();
    }

    /**
     * @param string $entityClassName
     * @param string|callable $labelOrCallback
     * @param callable|null $filterCallback
     * @return Column
     */
    public function setSelectEntityFilter(
        string $entityClassName,
        $labelOrCallback,
        ?callable $filterCallback = null
    ): self {
        $this->filterColumnNames = [$this->name];
        $this->filter = new SelectEntityFilter($entityClassName, $labelOrCallback, $filterCallback);
        return $this->setFilterable();
    }

    /**
     * @param string|string[]|null $filterColumnName
     * @return Column
     */
    public function setFilterable($filterColumnName = null): self
    {
        if ($filterColumnName !== null) {
            $this->filterColumnNames = is_array($filterColumnName) ? $filterColumnName : [$filterColumnName];
        }
        $this->filterable = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFilterable(): bool
    {
        return $this->filterable;
    }

    /**
     * @param string|string[]|null $sortColumnName
     * @return Column
     */
    public function setSortable($sortColumnName = null): self
    {
        if ($sortColumnName !== null) {
            $this->sortColumnNames = is_array($sortColumnName) ? $sortColumnName : [$sortColumnName];
        }
        $this->sortable = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * @return string|null
     */
    public function getCustomTemplate(): ?string
    {
        return $this->customTemplate;
    }

    /**
     * @param string $customTemplate
     * @return Column
     */
    public function setCustomTemplate(string $customTemplate): self
    {
        $this->customTemplate = $customTemplate;
        return $this;
    }

    /**
     * @param callable $customRendererCallback
     */
    public function setCustomRendererCallback(callable $customRendererCallback): void
    {
        $this->customRendererCallback = $customRendererCallback;
    }

    /**
     * @param callable $customFilterCallback
     * @return Column
     */
    public function setCustomFilterCallback(callable $customFilterCallback): self
    {
        $this->customFilterCallback = $customFilterCallback;
        return $this->setFilterable();
    }

    /**
     * @return callable|null
     */
    public function getCustomFilterCallback(): ?callable
    {
        return $this->customFilterCallback;
    }

    /**
     * @return callable|null
     */
    public function getCustomSortCallback(): ?callable
    {
        return $this->customSortCallback;
    }

    /**
     * @param callable $customSortCallback
     * @return Column
     */
    public function setCustomSortCallback(callable $customSortCallback): self
    {
        $this->customSortCallback = $customSortCallback;
        return $this->setSortable();
    }

    /**
     * @param mixed $row
     * @param EngineInterface $engine
     * @return string|null
     */
    public function renderContent($row, EngineInterface $engine): ?string
    {
        if ($this->customTemplate !== null) {
            return $engine->render($this->customTemplate, ['item' => $row]);
        }

        if (is_callable($this->customRendererCallback)) {
            return call_user_func($this->customRendererCallback, $row);
        }

        return null;
    }
}
