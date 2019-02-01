<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class SmokeTest extends PantherTestCase
{
    public function testSmoke(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/');

        self::assertSame('<title>DataGrid</title>', $crawler->filter('title')->html());

        $client->waitFor('tr');

        $expected = '<tbody>';
        $expected .= '<tr role="row" class="odd"><td class="sorting_1">name9</td><td>1.3.2019</td><td>1</td></tr>';
        $expected .= '<tr role="row" class="even"><td class="sorting_1">name3</td><td>1.1.2019</td><td>0</td></tr>';
        $expected .= '<tr role="row" class="odd"><td class="sorting_1">name2</td><td>1.2.2019</td><td>9</td></tr>';
        $expected .= '</tbody>';
        self::assertSame($expected, $crawler->filter('tbody')->html());
    }
}
