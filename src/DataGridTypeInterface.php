<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
interface DataGridTypeInterface
{
    /**
     * @param DataGridBuilder $builder
     */
    public function buildGrid(DataGridBuilder $builder): void;
}
