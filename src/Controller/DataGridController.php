<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Controller;

use FreezyBee\DataGridBundle\DataGridFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
    public function handle(string $name, Request $request): JsonResponse
    {
        $name = preg_replace('/\//', '\\', $name) ?? '';
        return $this->dataGridFactory->create($name)->ajax($request);
    }
}
