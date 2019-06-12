<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Column;

use Twig\Environment;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class TextColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    public function renderContent($row, Environment $engine, array $params = []): ?string
    {
        $content = parent::renderContent($row, $engine, $params);
        if ($content !== null) {
            return $content;
        }

        return $engine->render('@FreezyBeeDataGrid/column/text.html.twig', [
                'item' => $row,
                'propertyName' => $this->contentColumnName,
            ] + $params);
    }
}
