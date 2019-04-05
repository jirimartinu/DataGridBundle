<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Controller;

use FreezyBee\DataGridBundle\DataGridFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class DataGridController
{
    /** @var DataGridFactory */
    private $dataGridFactory;

    /**
     * @param DataGridFactory $dataGridFactory
     */
    public function __construct(DataGridFactory $dataGridFactory)
    {
        $this->dataGridFactory = $dataGridFactory;
    }

    /**
     * @param string $name
     * @param Request $request
     * @return JsonResponse
     */
    public function ajax(string $name, Request $request): JsonResponse
    {
        return $this->dataGridFactory->create(
            self::processName($name),
            $request->query->get('options') ?? []
        )->ajax($request);
    }

    /**
     * @param string $name
     * @param string $format
     * @param Request $request
     * @return Response
     */
    public function export(string $name, string $format, Request $request): Response
    {
        return $this->dataGridFactory->create(
            self::processName($name),
            $request->query->get('options') ?? []
        )->export($request, $format);
    }

    /**
     * @param string $name
     * @return string
     */
    private static function processName(string $name): string
    {
        return preg_replace('/\//', '\\', $name) ?? '';
    }
}
