<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Column;

use FreezyBee\DataGridBundle\Filter\DateRangeFilter;
use FreezyBee\DataGridBundle\Filter\Filter;
use FreezyBee\DataGridBundle\Filter\NumberCompareFilter;
use FreezyBee\DataGridBundle\Filter\NumberRangeFilter;
use FreezyBee\DataGridBundle\Filter\SelectBooleanFilter;
use FreezyBee\DataGridBundle\Filter\SelectEntityFilter;
use FreezyBee\DataGridBundle\Filter\SelectFilter;
use Twig\Environment;
use FreezyBee\DataGridBundle\Filter\TextFilter;
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

    /** @var string[] */
    protected $allowExport = [];

    /** @var bool */
    protected $allowRender = true;

    /** @var array */
    protected $templateParams = [];

    /**
     * @var callable|null
     * callable(mixed $value, array $params)
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
     * @return Column
     */
    public function setTextFilter(?string $placeholder = null): self
    {
        $this->filter = new TextFilter();
        $this->filter->setPlaceholder($placeholder ?? $this->getLabel());
        return $this->setFilterable();
    }

    /**
     * @param array $options
     * @return Column
     */
    public function setSelectFilter(array $options, ?string $placeholder = null): self
    {
        $this->filter = new SelectFilter($options);
        $this->filter->setPlaceholder($placeholder ?? $this->getLabel());
        return $this->setFilterable();
    }

    /**
     * @return Column
     */
    public function setSelectBooleanFilter(?string $placeholder = null): self
    {
        $this->filter = new SelectBooleanFilter();
        $this->filter->setPlaceholder($placeholder ?? $this->getLabel());
        return $this->setFilterable();
    }

    /**
     * @return Column
     */
    public function setDateRangeFilter(?string $placeholder = null): self
    {
        $this->filter = new DateRangeFilter();
        $this->filter->setPlaceholder($placeholder ?? $this->getLabel());
        return $this->setFilterable();
    }

    /**
     * @return Column
     */
    public function setNumberCompareFilter(?string $placeholder = null): self
    {
        $this->filter = new NumberCompareFilter();
        $this->filter->setPlaceholder($placeholder ?? $this->getLabel());
        return $this->setFilterable();
    }

    /**
     * @return Column
     */
    public function setNumberRangeFilter(?string $placeholder = null): self
    {
        $this->filter = new NumberRangeFilter();
        $this->filter->setPlaceholder($placeholder ?? $this->getLabel());
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
        ?callable $filterCallback = null,
        ?string $placeholder = null
    ): self {
        $this->filterColumnNames = [$this->name];
        $this->filter = new SelectEntityFilter($entityClassName, $labelOrCallback, $filterCallback);
        $this->filter->setPlaceholder($placeholder ?? $this->getLabel());
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
     * @param string|null $placeholder
     * @return Column
     */
    public function setFilterPlaceholder(?string $placeholder = ''): self
    {
        if ($this->filter instanceof Filter) {
            $this->filter->setPlaceholder($placeholder);
        }
        return $this;
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
     * @param array $params
     * @return Column
     */
    public function setCustomTemplate(string $customTemplate, array $params = []): self
    {
        $this->customTemplate = $customTemplate;
        if ($params) {
            $this->templateParams = $params;
        }
        return $this;
    }

    /**
     * @param string $format
     * @return bool
     */
    public function isAllowExport(string $format): bool
    {
        return in_array($format, $this->allowExport, true);
    }

    /**
     * @param string[] $allowExport
     * @return Column
     */
    public function setAllowExport(array $allowExport = []): self
    {
        $this->allowExport = $allowExport;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowRender(): bool
    {
        return $this->allowRender;
    }

    /**
     * @param bool $allowRender
     * @return Column
     */
    public function setAllowRender(bool $allowRender): self
    {
        $this->allowRender = $allowRender;
        return $this;
    }

    /**
     * @param callable $customRendererCallback
     * @return Column
     */
    public function setCustomRendererCallback(callable $customRendererCallback): self
    {
        $this->customRendererCallback = $customRendererCallback;
        return $this;
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
     * @return array
     */
    public function getTemplateParams(): array
    {
        return $this->templateParams;
    }

    /**
     * @param array $params
     * @return Column
     */
    public function setTemplateParams(array $params): self
    {
        $this->templateParams = $params;
        return $this;
    }

    /**
     * @param mixed $row
     * @param Environment $engine
     * @param array $params
     * @return string|null
     */
    public function renderContent($row, Environment $engine, array $params = []): ?string
    {
        if ($this->customTemplate !== null) {
            return $engine->render($this->customTemplate, $params + $this->templateParams + ['item' => $row, 'propertyName' => $this->contentColumnName]);
        }

        if (is_callable($this->customRendererCallback)) {
            return call_user_func($this->customRendererCallback, $row, $params);
        }

        return null;
    }
}
