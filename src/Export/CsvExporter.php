<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Export;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class CsvExporter implements DataGridExporterInterface
{
    /** @var SerializerInterface */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function export(array $data, string $format = null): Response
    {
        $csv = $this->serializer->serialize($data, 'csv', [
            CsvEncoder::DELIMITER_KEY => ';',
        ]);

        $response = new Response(\chr(239) . \chr(187) . \chr(191) . $csv);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');
        return $response;
    }
}
