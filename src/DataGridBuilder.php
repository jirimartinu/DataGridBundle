<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle;

use Doctrine\ORM\QueryBuilder;
use FreezyBee\DataGridBundle\Column\Action;
use FreezyBee\DataGridBundle\Column\ActionColumn;
use FreezyBee\DataGridBundle\Column\Column;
use FreezyBee\DataGridBundle\Column\DateTimeColumn;
use FreezyBee\DataGridBundle\Column\LinkColumn;
use FreezyBee\DataGridBundle\Column\TextColumn;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class DataGridBuilder
{
    /** @var QueryBuilder|array|null */
    private $dataSource;

    /** @var Column[] */
    private $columns = [];

    /** @var ActionColumn */
    private $actionColumn;

    /**
     */
    public function __construct()
    {
        $this->actionColumn = new ActionColumn();
    }

    /**
     * @param array|QueryBuilder $dataSource
     * @return DataGridBuilder
     */
    public function setDataSource($dataSource): self
    {
        $this->dataSource = $dataSource;
        return $this;
    }

    /**
     * @return array|QueryBuilder|null
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * @param string $name
     * @param string $label
     * @param string|null $contentColumnName
     * @return TextColumn
     */
    public function addText(string $name, string $label, ?string $contentColumnName = null): TextColumn
    {
        return $this->columns[] = new TextColumn($name, $label, $contentColumnName);
    }

    /**
     * @param string $name
     * @param string $label
     * @param string $format
     * @return DateTimeColumn
     */
    public function addDateTime(string $name, string $label, string $format = 'd.m.Y H:i'): DateTimeColumn
    {
        return $this->columns[] = new DateTimeColumn($name, $label, $format);
    }

    /**
     * @param string $name
     * @param string $label
     * @param string $route
     * @param string|null $contentColumnName
     * @return LinkColumn
     */
    public function addLink(string $name, string $label, string $route, ?string $contentColumnName = null): LinkColumn
    {
        return $this->columns[] = new LinkColumn($name, $label, $route, $contentColumnName);
    }

    /**
     * @param string $route
     * @param string $label
     * @param array $params
     * @return Action
     */
    public function addAction(string $route, string $label, array $params = []): Action
    {
        return $this->actionColumn->addAction($route, $label, $params);
    }

    /**
     * @param Column $column
     * @return Column
     */
    public function add(Column $column): Column
    {
        return $this->columns[] = $column;
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
}
