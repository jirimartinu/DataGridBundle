<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Filter;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class SelectBooleanFilter extends SelectFilter
{
    /**
     */
    public function __construct()
    {
        parent::__construct(['Ano' => 1, 'Ne' => 0]);
    }
}
