<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Tests\E2E;

use FreezyBee\DataGridBundle\Tests\App\BeeGridType;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Panther\PantherTestCase;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class SmokeTest extends PantherTestCase
{
    public function testAjax(): void
    {
        $client = self::createPantherClient();
        $crawler = $client->request('GET', '/');

        self::assertSame('<title>DataGrid</title>', $crawler->filter('title')->html());

        $client->waitFor('tbody tr');

        $expected = '<tbody>';
        $expected .= '<tr role="row" class="odd"><td class="sorting_1">name9</td><td>1.3.2019</td><td>1</td></tr>';
        $expected .= '<tr role="row" class="even"><td class="sorting_1">name3</td><td>1.1.2019</td><td>0</td></tr>';
        $expected .= '<tr role="row" class="odd"><td class="sorting_1">name2</td><td>1.2.2019</td><td>9</td></tr>';
        $expected .= '</tbody>';
        self::assertSame($expected, $crawler->filter('tbody')->html());
    }

    public function testExport(): void
    {
        $query = [
            'draw' => '1',
            'columns' => [
                [
                    'data' => '0',
                    'name' => 'a',
                    'searchable' => 'true',
                    'orderable' => 'false',
                    'search' => [
                        'value' => '',
                        'regex' => 'false',
                    ],
                ],
                [
                    'data' => '1',
                    'name' => 'b',
                    'searchable' => 'true',
                    'orderable' => 'false',
                    'search' => [
                        'value' => '',
                        'regex' => 'false',
                    ],
                ],
                [
                    'data' => '2',
                    'name' => 'c',
                    'searchable' => 'true',
                    'orderable' => 'false',
                    'search' => [
                        'value' => '',
                        'regex' => 'false',
                    ],
                ],
            ],
            'order' => [
                [
                    'column' => '0',
                    'dir' => 'desc',
                ],
            ],
            'start' => '0',
            'length' => '10',
            'search' => [
                'value' => '',
                'regex' => 'false',
            ],
        ];

        /** @var Client $client */
        $client = self::createClient();
        $client->request('GET', '/datagrid/export/' . BeeGridType::class, $query);

        $expected = "name9,1.3.2019,1,Ano\n";
        $expected .= "name3,1.1.2019,0,Ne\n";
        $expected .= 'name2,1.2.2019,9,Ano';

        $response = $client->getResponse();
        self::assertContains('text/csv', $response->headers->get('Content-Type'));
        self::assertContains('attachment; filename="export.csv"', $response->headers->get('Content-Disposition'));
        self::assertContains($expected, $response->getContent());
    }
}