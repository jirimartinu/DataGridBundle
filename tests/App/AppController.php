<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Tests\App;

use FreezyBee\DataGridBundle\DataGridFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

class AppController
{
    /** @var EngineInterface */
    private $engine;

    /** @var DataGridFactory */
    private $dataGridFactory;

    public function __construct(EngineInterface $engine, DataGridFactory $dataGridFactory)
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
