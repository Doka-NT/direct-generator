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
        $expected = 'bar		title bar		text bar
bar		title bar		bar text
bar		bar title		text bar
bar		bar title		bar text
baz		title baz		text baz
baz		title baz		baz text
baz		baz title		text baz
baz		baz title		baz text
';

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
}
