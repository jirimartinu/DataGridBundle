<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Tests\App;

use DateTime;
use FreezyBee\DataGridBundle\DataGridBuilder;
use FreezyBee\DataGridBundle\DataGridTypeInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class BeeGridType implements DataGridTypeInterface
{
    /**
     * @param DataGridBuilder $builder
     */
    public function buildGrid(DataGridBuilder $builder): void
    {
        $builder->setDataSource([
            ['a' => 'name9', 'b' => new DateTime('2019-03-01'), 'c' => 1, 'd' => true],
            ['a' => 'name2', 'b' => new DateTime('2019-02-01'), 'c' => 9, 'd' => true],
            ['a' => 'name3', 'b' => new DateTime('2019-01-01'), 'c' => 0, 'd' => false],
        ]);

        $builder->addText('a', 'A')
            ->setTextFilter('A')
            ->setSortable()
            ->setAllowExport(['csv']);

        $builder->addDateTime('b', 'B', 'j.n.Y')
            ->setDateRangeFilter()
            ->setSortable()
            ->setAllowExport(['csv']);

        $builder->addText('c', 'C')
            ->setNumberRangeFilter()
            ->setSortable();

        $builder->addText('d', 'D')
            ->setAllowRender(false)
            ->setAllowExport(['csv']);

        $builder->setAllowExport(['csv']);
    }
}
