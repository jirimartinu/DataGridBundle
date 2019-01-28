<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FreezyBeeDataGridBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        if ($this->extension === null) {
            $this->extension = $this->createContainerExtension();
        }

        return $this->extension;
    }
}
