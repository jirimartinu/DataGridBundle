<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Export;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
interface DataGridExporterInterface
{
    /**
     * @param array $data
     * @param string|null $format
     * @return Response
     */
    public function export(array $data, string $format = null): Response;
}
