<?php

namespace Osteel\Kobwise\Tests\Commands;

use Osteel\Kobwise\Commands\Convert;
use Osteel\Kobwise\Tests\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ConvertTest extends TestCase
{
    public function testItConvertsTheAnnotationsToHighlights()
    {
        $sut    = new Convert();
        $tester = new CommandTester($sut);

        $tester->setInputs(['Foo Bar', 'https://foo.bar']);
        $tester->execute(['file' => sprintf('%s/../stubs/test.txt', __DIR__)]);

        $result = sprintf('%s/../../test.csv', __DIR__);

        $this->assertTrue(file_exists($result));

        $resource = fopen($result, 'r');

        $rows = [];
        while (! feof($resource)) {
            $rows[] = fgetcsv($resource);
        }

        fclose($resource);

        $this->assertCount(10, $rows);
        $this->assertEquals(['Highlight', 'Note', 'Title', 'Author', 'URL'], $rows[0]);
        $this->assertEquals(
            [
                'There’s not really that much to fear in terms of failure, and so people should take on a lot more accountability than they do.',
                'Love this passage',
                'The Almanack of Naval Ravikant: A Guide to Wealth and Happiness',
                'Foo Bar',
                'https://foo.bar'
            ],
            $rows[1]
        );

        $this->assertEquals(
            [
                'We’ve shared a lot of meals, shared a lot of deals, and hopped around the world together.',
                'Test with note and accentué',
                'The Almanack of Naval Ravikant: A Guide to Wealth and Happiness',
                'Foo Bar',
                'https://foo.bar'
            ],
            $rows[7]
        );

        unlink($result);
    }
}
