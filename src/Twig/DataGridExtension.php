<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use FreezyBee\DataGridBundle\DataGrid;
use FreezyBee\DataGridBundle\Filter\DateRangeFilter;
use FreezyBee\DataGridBundle\Filter\Filter;
use FreezyBee\DataGridBundle\Filter\NumberCompareFilter;
use FreezyBee\DataGridBundle\Filter\NumberRangeFilter;
use FreezyBee\DataGridBundle\Filter\SelectEntityFilter;
use FreezyBee\DataGridBundle\Filter\SelectFilter;
use FreezyBee\DataGridBundle\Filter\TextFilter;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class DataGridExtension extends AbstractExtension
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var PropertyAccessorInterface */
    private $propertyAccessor;

    /**
     * @param EntityManagerInterface $entityManager
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(EntityManagerInterface $entityManager, PropertyAccessorInterface $propertyAccessor)
    {
        $this->entityManager = $entityManager;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('datagrid', [$this, 'render'], ['is_safe' => ['html']]),
            new TwigFunction(
                'datagrid_filter',
                [$this, 'renderFilter'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new TwigFunction('datagrid_accessor', [$this, 'accessor'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param DataGrid $dataGrid
     * @return string
     */
    public function render(DataGrid $dataGrid): string
    {
        return $dataGrid->render();
    }

    /**
     * @param Environment $engine
     * @param Filter|null $filter
     * @param string $name
     * @param int $index
     * @return string
     */
    public function renderFilter(Environment $engine, ?Filter $filter, string $name, int $index): string
    {
        $template = '';

        switch (true) {
            case $filter instanceof SelectEntityFilter:
                $labelOrCallback = $filter->getLabelOrCallback();
                $filterCallback = $filter->getFilterCallback();
                $items = [];

                if (is_callable($labelOrCallback)) {
                    $qb = $this->entityManager->createQueryBuilder()
                        ->select('i')
                        ->from($filter->getEntityClassName(), 'i');

                    if ($filterCallback !== null) {
                        $filterCallback($qb);
                    }

                    foreach ($qb->getQuery()->getResult() as $row) {
                        if (method_exists($row, 'getId')) {
                            $items[$labelOrCallback($row)] = $row->getId();
                        }
                    }
                } else {
                    $qb = $this->entityManager->createQueryBuilder()
                        ->select("i.$labelOrCallback, i.id")
                        ->from($filter->getEntityClassName(), 'i')
                        ->indexBy('i', 'i.id');

                    if ($filterCallback !== null) {
                        $filterCallback($qb);
                    }

                    $items = array_map('reset', $qb->getQuery()->getArrayResult());
                    $items = array_flip($items);
                }

                return self::renderHtmlSelect($engine, $items, $name, $index, $filter->getPlaceholder());
            case $filter instanceof SelectFilter:
                return self::renderHtmlSelect($engine, $filter->getOptions(), $name, $index, $filter->getPlaceholder());
            case $filter instanceof DateRangeFilter:
                $template = '@FreezyBeeDataGrid/filter/date_range_picker.html.twig';
                break;
            case $filter instanceof NumberRangeFilter:
                $template = '@FreezyBeeDataGrid/filter/number_range_picker.html.twig';
                break;
            case $filter instanceof NumberCompareFilter:
                $template = '@FreezyBeeDataGrid/filter/number_compare.html.twig';
                break;
            case $filter instanceof TextFilter:
                $template = '@FreezyBeeDataGrid/filter/text.html.twig';
                break;
        }

        return $engine->render($template, [
            'name' => $name,
            'index' => $index,
            'placeholder' => $filter->getPlaceholder()
        ]);
    }

    /**
     * @param mixed $item
     * @param string $propertyPath
     * @return mixed
     */
    public function accessor($item, string $propertyPath)
    {
        try {
            if (is_array($item)) {
                $propertyPath = "[$propertyPath]";
            }
            return $this->propertyAccessor->getValue($item, $propertyPath);
        } catch (UnexpectedTypeException $e) {
            return null;
        }
    }

    /**
     * @param Environment $engine
     * @param array $options
     * @param string $name
     * @param int $index
     * @param string $placeholder
     * @return string
     */
    private static function renderHtmlSelect(Environment $engine, array $options, string $name, int $index, string $placeholder): string
    {
        return $engine->render('@FreezyBeeDataGrid/filter/select.html.twig', [
            'options' => $options,
            'name' => $name,
            'index' => $index,
            'placeholder' => $placeholder
        ]);
    }
}
