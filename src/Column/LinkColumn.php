<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Column;

use Symfony\Component\Templating\EngineInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class LinkColumn extends Column
{
    /** @var string */
    private $route;

    /**
     * @param string $name
     * @param string $label
     * @param string $route
     * @param string|null $contentColumnName
     */
    public function __construct(string $name, string $label, string $route, ?string $contentColumnName = null)
    {
        parent::__construct($name, $label, $contentColumnName);
        $this->route = $route;
    }

    /**
     * {@inheritdoc}
     */
    public function renderContent($row, EngineInterface $engine, array $params = []): ?string
    {
        $content = parent::renderContent($row, $engine, $params);
        if ($content !== null) {
            return $content;
        }

        return $engine->render('@FreezyBeeDataGrid/column/link.html.twig', [
                'item' => $row,
                'linkIdPropertyName' => "{$this->name}.id",
                'propertyName' => $this->contentColumnName,
                'route' => $this->route,
            ] + $params);
    }
}
