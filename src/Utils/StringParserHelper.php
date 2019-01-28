<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Utils;

use DateTime;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class StringParserHelper
{
    /**
     * @param string $value
     * @return DateTime[]
     */
    public static function parseStringToDateArray(string $value): array
    {
        if (strpos($value, ' do ') === false) {
            $from = $value;
            $to = $value;
        } else {
            [$from, $to] = explode(' do ', $value, 2);
        }

        return [new DateTime($from), (new DateTime($to))->setTime(23, 59, 59)];
    }

    /**
     * @param string $value
     * @return int[]
     */
    public static function parseStringToNumberArray(string $value): array
    {
        if (strpos($value, '-') === false) {
            $from = $value;
            $to = PHP_INT_MAX;
        } else {
            [$from, $to] = explode('-', $value, 2);
        }

        return [(int) $from, (int) $to];
    }
}
