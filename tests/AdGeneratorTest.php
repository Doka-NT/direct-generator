<?php

namespace tests\skobka\dg;

use PHPUnit\Framework\TestCase;
use skobka\dg\AdGenerator;
use skobka\dg\DgParser;

/**
 * @coversDefaultClass \skobka\dg\AdGenerator
 */
class AdGeneratorTest extends TestCase
{
    /**
     * @return void
     */
    public function testGenerate(): void
    {
        $expected = implode([
                $this->createRow('bar 1', 'bar', 'title bar', 'text bar'),
                $this->createRow('bar 1', 'bar', 'title bar', 'bar text'),
                $this->createRow('bar 1', 'bar', 'bar title', 'text bar'),
                $this->createRow('bar 1', 'bar', 'bar title', 'bar text'),
                $this->createRow('baz 1', 'baz', 'title baz', 'text baz'),
                $this->createRow('baz 1', 'baz', 'title baz', 'baz text'),
                $this->createRow('baz 1', 'baz', 'baz title', 'text baz'),
                $this->createRow('baz 1', 'baz', 'baz title', 'baz text'),
            ], \PHP_EOL) . \PHP_EOL;

        /* @var $parser DgParser|\PHPUnit_Framework_MockObject_MockObject */
        $parser = $this
            ->getMockBuilder(DgParser::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'parse',
                'getKeywords',
                'getTitles',
                'getTexts',
            ])
            ->getMock();

        $parser->expects($this->once())->method('parse')->with('foo');
        $parser->method('getKeywords')->willReturn(['bar', 'baz']);
        $parser->method('getTitles')->willReturn(['title [key]', '[key] title']);
        $parser->method('getTexts')->willReturn(['text [key]', '[key] text']);

        $output = \tempnam(\sys_get_temp_dir(), 'ad-generator');

        $generator = new AdGenerator($parser, $output);

        $generator->generate('foo');

        $this->assertSame($expected, \file_get_contents($output));
    }

    /**
     * @param string $group
     * @param string $keyword
     * @param string $title
     * @param string $text
     * @return string
     */
    private function createRow(
        string $group,
        string $keyword,
        string $title,
        string $text
    ): string {
        return implode("\t\t", [
            '-',
            'Текстово-графическое',
            '-',
            '',
            $group,
            '',
            '-',
            '',
            $keyword,
            '',
            '',
            $title,
            '',
            $text,
        ]);
    }
}
