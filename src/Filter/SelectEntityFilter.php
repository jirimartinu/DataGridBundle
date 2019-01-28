<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Filter;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class SelectEntityFilter extends Filter
{
    /** @var string */
    private $entityClassName;

    /** @var callable|string */
    private $labelOrCallback;

    /** @var callable|null */
    private $filterCallback;

    /**
     * @param string $entityClassName
     * @param string|callable $labelOrCallback
     * @param callable|null $filterCallback
     */
    public function __construct(string $entityClassName, $labelOrCallback, ?callable $filterCallback)
    {
        $this->entityClassName = $entityClassName;
        $this->labelOrCallback = $labelOrCallback;
        $this->filterCallback = $filterCallback;
    }

    /**
     * @return string
     */
    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }

    /**
     * @return callable|string
     */
    public function getLabelOrCallback()
    {
        return $this->labelOrCallback;
    }

    /**
     * @return callable|null
     */
    public function getFilterCallback(): ?callable
    {
        return $this->filterCallback;
    }
}
