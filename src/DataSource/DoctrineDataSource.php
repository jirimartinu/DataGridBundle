<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\DataSource;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use FreezyBee\DataGridBundle\Column\Column;
use FreezyBee\DataGridBundle\Filter\DateRangeFilter;
use FreezyBee\DataGridBundle\Filter\SelectEntityFilter;
use FreezyBee\DataGridBundle\Filter\SelectFilter;
use FreezyBee\DataGridBundle\Filter\TextFilter;
use FreezyBee\DataGridBundle\Utils\DataGridMagicHelper;
use FreezyBee\DataGridBundle\Utils\StringParserHelper;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class DoctrineDataSource implements DataSourceInterface
{
    /** @var QueryBuilder */
    private $queryBuilder;

    /** @var string */
    private $rootAlias;

    /** @var int */
    private $paramCounter = 0;

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->rootAlias = $this->queryBuilder->getRootAliases()[0];
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount(): int
    {
        return (int) (clone $this->queryBuilder)
            ->select("COUNT ($this->rootAlias.id)")
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getFilteredCount(): int
    {
        return (new Paginator($this->queryBuilder))->count();
    }

    /**
     * {@inheritdoc}
     */
    public function applySort(Column $column, string $direction): void
    {
        $customOrder = $column->getCustomSortCallback();
        if ($customOrder !== null) {
            $customOrder($this->queryBuilder, $direction);
        } else {
            foreach ($column->getSortColumnNames() as $sortColumnName) {
                $this->queryBuilder->addOrderBy($this->resolveColumnPath($sortColumnName), $direction);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function applyFilter(Column $column, $value): void
    {
        if ($column->getCustomFilterCallback() !== null) {
            $column->getCustomFilterCallback()($this->queryBuilder, $value);
        } elseif ($column->getFilter()) {
            $filter = $column->getFilter();
            if ($filter instanceof SelectEntityFilter || $filter instanceof SelectFilter) {
                $this->queryBuilder
                    ->andWhere("{$this->rootAlias}.{$column->getFilterColumnNames()[0]} = ?$this->paramCounter")
                    ->setParameter($this->paramCounter++, $value);
            } elseif ($filter instanceof DateRangeFilter) {
                [$from, $to] = StringParserHelper::parseStringToDateArray($value);
                $whereName = "{$this->rootAlias}.{$column->getFilterColumnNames()[0]}";

                $fromParam = $this->paramCounter++;
                $toParam = $this->paramCounter++;

                $this->queryBuilder
                    ->andWhere("$whereName >= ?$fromParam AND $whereName <= ?$toParam")
                    ->setParameter($fromParam, $from)
                    ->setParameter($toParam, $to);
            } elseif ($filter instanceof TextFilter) {
                $whereQuery = [];
                foreach ($column->getFilterColumnNames() as $filterColumnName) {
                    $whereQuery[] = "{$this->resolveColumnPath($filterColumnName)} LIKE ?$this->paramCounter";
                }

                $this->queryBuilder
                    ->andWhere(implode(' OR ', $whereQuery))
                    ->setParameter($this->paramCounter++, "%$value%");
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function applyLimitAndOffset(int $limit, int $offset): void
    {
        $this->queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($limit);
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        return iterator_to_array(new Paginator($this->queryBuilder));
    }

    /**
     * @param string $columnPath
     * @return string
     */
    private function resolveColumnPath(string $columnPath): string
    {
        if (strpos($columnPath, '.') === false) {
            return "$this->rootAlias.{$columnPath}";
        }

        // add to join
        [$entity] = explode('.', $columnPath);
        DataGridMagicHelper::trySafelyApplyJoin($this->queryBuilder, "$this->rootAlias.$entity");

        return $columnPath;
    }
}
