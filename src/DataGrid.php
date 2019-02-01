<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle;

use FreezyBee\DataGridBundle\Column\ActionColumn;
use FreezyBee\DataGridBundle\DataSource\DataSourceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class DataGrid
{
    /** @var EngineInterface */
    private $engine;

    /** @var DataSourceInterface */
    private $dataSource;

    /** @var DataGridConfig */
    private $config;

    /** @var string */
    private $name;

    /**
     * @param EngineInterface $engine
     * @param DataSourceInterface $dataSource
     * @param DataGridConfig $config
     * @param string $name
     */
    public function __construct(
        EngineInterface $engine,
        DataSourceInterface $dataSource,
        DataGridConfig $config,
        string $name
    ) {
        $this->engine = $engine;
        $this->dataSource = $dataSource;
        $this->config = $config;
        $this->name = $name;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ajax(Request $request): JsonResponse
    {
        $totalCount = $this->dataSource->getTotalCount();

        $query = $request->query->all();

        // sort
        $orderByIndex = $query['order'][0]['column'] ?? null;
        if ($orderByIndex !== null) {
            $this->dataSource->applySort($this->config->getColumns()[$orderByIndex], $query['order'][0]['dir']);
        }

        // filters
        foreach ($query['columns'] as $index => $ajaxColumn) {
            $value = $ajaxColumn['search']['value'];
            $column = $this->config->getColumns()[$index] ?? null;

            if ($value !== '' && $column !== null && $column->isFilterable()) {
                $this->dataSource->applyFilter($column, $value);
            }
        }

        $filteredCount = $this->dataSource->getFilteredCount();

        // limit and offset
        $this->dataSource->applyLimitAndOffset((int) $query['length'], (int) $query['start']);

        $items = $this->dataSource->getData();

        $data = [];
        foreach ($items as $item) {
            $row = [];
            foreach ($this->config->getColumns() as $column) {
                if ($column instanceof ActionColumn) {
                    continue;
                }
                $row[] = $column->renderContent($item, $this->engine);
            }

            if ($this->config->getActionColumn()->hasActions()) {
                $row[] = $this->engine->render('@FreezyBeeDataGrid/action.html.twig', [
                    'item' => $item,
                    'actions' => $this->config->getActionColumn()->getActions()
                ]);
            }
            $data[] = $row;
        }

        return (new JsonResponse([
            'draw' => $request->query->getInt('draw'),
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $filteredCount,
            'data' => $data,
        ]))->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $sortIndex = 0;
        if ($this->config->getDefaultSortColumnName() !== null) {
            $sortIndex = array_search($this->config->getDefaultSortColumnName(), $this->config->getColumns(), true);
        }

        return $this->engine->render('@FreezyBeeDataGrid/grid.html.twig', [
            'name' => $this->name,
            'columns' => $this->config->getColumns(),
            'actionColumn' => $this->config->getActionColumn(),
            'default' => [
                'perPage' => $this->config->getDefaultPerPage(), $this->config->getColumns(),
                'sortIndex' => $sortIndex,
                'sortDir' => $this->config->getDefaultSortColumnDirection() ?? 'desc',
            ]
        ]);
    }
}
