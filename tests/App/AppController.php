<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Tests\App;

use FreezyBee\DataGridBundle\DataGridFactory;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class AppController
{
    /** @var Environment */
    private $engine;

    /** @var DataGridFactory */
    private $dataGridFactory;

    public function __construct(Environment $engine, DataGridFactory $dataGridFactory)
    {
        $this->engine = $engine;
        $this->dataGridFactory = $dataGridFactory;
    }

    public function index(): Response
    {
        $grid = $this->dataGridFactory->create(BeeGridType::class);
        return new Response($this->engine->render('index.html.twig', [
            'grid' => $grid,
        ]));
    }
}
