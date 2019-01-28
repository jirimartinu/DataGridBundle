<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Exception;

use Exception;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class DataGridException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
