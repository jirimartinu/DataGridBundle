<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Controller;

use FreezyBee\DataGridBundle\DataGridFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/api/grid/{name}", name="datagrid_ajax")
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
