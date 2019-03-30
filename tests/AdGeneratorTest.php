<?php

namespace tests\skobka\dg;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use skobka\dg\AdGenerator;
use skobka\dg\Exceptions\GenerateException;
use skobka\dg\ParserInterface;
use skobka\dg\Parsers\DG\FileParser;
use skobka\dg\View;
use function file_get_contents;
use function mb_strlen;
use function ob_get_clean;
use function ob_start;
use function sys_get_temp_dir;
use function tempnam;
use function uniqid;
use const PHP_EOL;

/**
 * @coversDefaultClass \skobka\dg\AdGenerator
 */
class AdGeneratorTest extends TestCase
{
    /**
     * @return void
     */
    public function testGenerate()
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
            ], PHP_EOL) . PHP_EOL;

        /* @var $parser FileParser|PHPUnit_Framework_MockObject_MockObject */
        $parser = $this
            ->getMockBuilder(FileParser::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'parse',
                'getKeywords',
                'getTitles',
                'getTexts',
            ])
            ->getMock();

        $view = new View();

        $parser->expects($this->once())->method('parse')->with('foo');
        $parser->method('getKeywords')->willReturn(['bar', 'baz']);
        $parser->method('getTitles')->willReturn(['title [key]', '[key] title']);
        $parser->method('getTexts')->willReturn(['text [key]', '[key] text']);

        $output = tempnam(sys_get_temp_dir(), 'ad-generator');

        $generator = new AdGenerator($parser, $view, $output);

        $generator->generate('foo');

        $this->assertSame($expected, file_get_contents($output));
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

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testRenderException()
    {
        $message = uniqid('foo', false);
        $exception = new GenerateException($message);
        $count = mb_strlen($message);

        $parser = new FileParser();
        $view = new View();
        $generator = new AdGenerator($parser, $view, 'foo');

        $reflection = new ReflectionMethod(AdGenerator::class, 'renderException');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($generator, $exception);

        $property = new ReflectionProperty(AdGenerator::class, 'wasError');
        $property->setAccessible(true);

        $this->assertTrue($property->getValue($generator));
        $this->assertContains($message, $result);
        $this->assertContains("[$count]", $result);
    }

    /**
     * @return void
     */
    public function testNoOutputOnError()
    {
        /* @var $parser ParserInterface|PHPUnit_Framework_MockObject_MockObject */
        $parser = $this
            ->getMockBuilder(ParserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        /* @var $view View|PHPUnit_Framework_MockObject_MockObject */
        $view = $this
            ->getMockBuilder(View::class)
            ->disableOriginalConstructor()
            ->getMock();

        $output = tempnam(sys_get_temp_dir(), 'adGenerator-test');
        $generator = new AdGenerator($parser, $view, $output);

        $parser->method('getKeywords')->willReturn(['foo', 'bar']);
        $parser->method('getTitles')->willReturn(['foo', 'bar']);
        $parser->method('getTexts')->willReturn(['foo', 'bar']);
        $view->method('renderString')->willThrowException(new GenerateException());

        ob_start();
        $generator->generate('not used');
        ob_get_clean();

        $this->assertSame('', file_get_contents($output));
    }
}
