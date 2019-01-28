<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Utils;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class DataGridMagicHelper
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param string $joinString
     * @return QueryBuilder
     */
    public static function trySafelyApplyJoin(QueryBuilder $queryBuilder, string $joinString): QueryBuilder
    {
        [$rootAlias, $alias] = explode('.', $joinString);

        /** @var Join[] $joins */
        foreach ($queryBuilder->getDQLPart('join') as $dRootAlias => $joins) {
            if ($dRootAlias !== $rootAlias) {
                continue;
            }

            foreach ($joins as $join) {
                if ($join->getAlias() === $alias) {
                    return $queryBuilder;
                }
            }
        }

        $queryBuilder->leftJoin($joinString, $alias);
        return $queryBuilder;
    }
}
