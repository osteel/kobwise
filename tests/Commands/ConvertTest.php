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

        $this->assertCount(9, $rows);
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
                'It’s ownership versus wage work. If you are paid for renting out your time, even lawyers and doctors, you can make some money, but you’re not going to make the money that gives you financial freedom.',
                '',
                'The Almanack of Naval Ravikant: A Guide to Wealth and Happiness',
                'Foo Bar',
                'https://foo.bar'
            ],
            $rows[2]
        );

        $this->assertEquals(
            [
                'Without ownership, when you’re sleeping, you’re not earning. When you’re retired, you’re not earning. When you’re on vacation, you’re not earning. And you can’t earn nonlinearly.
If you look at even doctors who get rich (like really rich),',
                '',
                'The Almanack of Naval Ravikant: A Guide to Wealth and Happiness',
                'Foo Bar',
                'https://foo.bar'
            ],
            $rows[4]
        );

        $this->assertEquals(
            [
                'We’ve shared a lot of meals, shared a lot of deals, and hopped around the world together.',
                'Test with note and accentué',
                'The Almanack of Naval Ravikant: A Guide to Wealth and Happiness',
                'Foo Bar',
                'https://foo.bar'
            ],
            $rows[6]
        );

        $this->assertEquals(
            [
                'He can be as blunt as a foot to the face, but that’s part of what I love and respect about him: you never have to guess what Naval is thinking. I’ve never had to guess how he’s feeling about me, someone else, or a situation. This is a huge relief in a world of double-talk an...',
                'Test with note on
multiple lines',
                'The Almanack of Naval Ravikant: A Guide to Wealth and Happiness',
                'Foo Bar',
                'https://foo.bar'
            ],
            $rows[7]
        );

        unlink($result);
    }
}
