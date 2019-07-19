<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Column;

use Twig\Environment;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class DateTimeColumn extends Column
{
    /** @var string */
    private $format;

    /**
     * @param string $name
     * @param string $label
     * @param string $format
     */
    public function __construct(string $name, string $label, string $format)
    {
        parent::__construct($name, $label);
        $this->format = $format;
    }

    /**
     * {@inheritdoc}
     */
    public function renderContent($row, Environment $engine, array $params = []): ?string
    {
        $content = parent::renderContent($row, $engine, $params);
        if ($content !== null) {
            return $content;
        }

        return $engine->render('@FreezyBeeDataGrid/column/datetime.html.twig', [
                'item' => $row,
                'propertyName' => $this->contentColumnName,
                'format' => $this->format,
            ] + $params);
    }
}
