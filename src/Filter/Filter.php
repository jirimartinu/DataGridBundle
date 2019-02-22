<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Filter;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
abstract class Filter
{
    /** @var string|null */
    private $placeholder;

    /**
     * @return string|null
     */
    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    /**
     * @param string|null $placeholder
     * @return Filter
     */
    public function setPlaceholder(?string $placeholder = null): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }
}
