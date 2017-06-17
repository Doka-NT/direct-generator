<?php

namespace tests\skobka\dg;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use skobka\dg\AdGenerator;
use skobka\dg\exceptions\GenerateException;
use skobka\dg\exceptions\TextTooLongException;
use skobka\dg\exceptions\TitleTooLongException;
use skobka\dg\Parser;

/**
 * Class AdGeneratorTest
 * @package tests\skobka\dg
 */
class AdGeneratorTest extends TestCase
{
	public function testRenderString()
	{
		$parser = $this->createParser();
		$keyword = 'keyword 1';
		$title = '[key] title';
		$text = '[key] text';
		$parser->method('getKeywords')->willReturn([$keyword]);
		$parser->method('getTitles')->willReturn([$title]);
		$parser->method('getTexts')->willReturn([$text]);
		/* @var $parser Parser */
		$generator = $this->createAdGenerator($parser);

		$this->assertEquals("keyword 1,keyword 1 title,keyword 1 text\n", $generator->renderString(
			$keyword,
			$title,
			$text
		));
	}

	public function testRenderStringLongTitleException()
	{
		$parser = $this->createParser();
		$keyword = 'keyword 1';
		$text = '[key] text';
		$parser->method('getKeywords')->willReturn([$keyword]);
		$parser->method('getTexts')->willReturn([$text]);
		$parser->method('getTitles')->willReturn(['[key] some title which longer then 33 symbols']);
		/* @var $parser Parser */
		$generator = $this->createAdGenerator($parser, ['renderException']);
		$generator->expects($this->once())->method('renderException')->willReturnCallback(
			function (GenerateException $e) {
				$this->assertInstanceOf(TitleTooLongException::class, $e);
			});

		$generator->generate();
	}

	public function testRenderStringLongTextException()
	{
		$parser = $this->createParser();
		$keyword = 'keyword 1';
		$title = '[key] title';
		$parser->method('getKeywords')->willReturn([$keyword]);
		$parser->method('getTitles')->willReturn([$title]);
		$parser->method('getTexts')->willReturn(['[key] ver very very long text which must be longer than 75 symbols. It is need to this test']);

		/* @var $parser Parser */
		$generator = $this->createAdGenerator($parser, ['renderException']);
		$generator->expects($this->once())->method('renderException')->willReturnCallback(
			function (GenerateException $e) {
				$this->assertInstanceOf(TextTooLongException::class, $e);
			});

		$generator->generate();

	}

	public function testGenerateException()
	{
		$parser = $this->createParser();
		$keyword = 'keyword 1';
		$text = '[key] text';
		$parser->method('getKeywords')->willReturn([$keyword]);
		$parser->method('getTexts')->willReturn([$text]);
		$parser->method('getTitles')->willReturn([
			'normal title',
			'[key] some title which longer then 33 symbols',
		]);
		/* @var $parser Parser */
		$generator = $this->createAdGenerator($parser, ['renderException', 'flushOutput']);
		$generator->expects($this->once())->method('renderException')->willReturn('some output');
		$generator->expects($this->never())->method('flushOutput');

		$wasErrorProperty = new \ReflectionProperty(AdGenerator::class, 'wasError');
		$wasErrorProperty->setAccessible(true);
		$wasErrorProperty->setValue($generator, true);

		ob_start();
		$generator->generate();
		$buffer = ob_get_clean();

		$this->assertEquals($buffer, 'some output');
	}

	public function testRenderException()
	{
		$parser = $this->createParser();
		$keyword = 'keyword 1';
		$text = '[key] text';
		$parser->method('getKeywords')->willReturn([$keyword]);
		$parser->method('getTexts')->willReturn([$text]);
		$parser->method('getTitles')->willReturn(['[key] some title which longer then 33 symbols']);
		/* @var $parser Parser */
		$generator = $this->createAdGenerator($parser);

		$method = new \ReflectionMethod(AdGenerator::class, 'renderException');
		$method->setAccessible(true);
		$buffer = $method->invoke($generator, new TitleTooLongException("some error"));

		$this->assertStringStartsWith("\033[0;31m", $buffer);
		$this->assertStringEndsWith("[10]\033[0m\n", $buffer);
		$this->assertContains("[ERROR] ", $buffer);
		$this->assertContains("some error", $buffer);

	}

	/**
	 * @return PHPUnit_Framework_MockObject_MockObject
	 */
	private function createParser(): PHPUnit_Framework_MockObject_MockObject
	{
		$parser = $this
			->getMockBuilder(Parser::class)
			->disableOriginalConstructor()
			->getMock();

		return $parser;
	}

	/**
	 * @param Parser $parser
	 *
	 * @param array  $methods
	 *
	 * @return AdGenerator|PHPUnit_Framework_MockObject_MockObject
	 */
	private function createAdGenerator(Parser $parser, array $methods = null): AdGenerator
	{
		/* @var $parser Parser|PHPUnit_Framework_MockObject_MockObject */
		$generator = $this
			->getMockBuilder(AdGenerator::class)
			->setConstructorArgs([$parser, 'php://stdout'])
			->setMethods($methods)
			->getMock();
		/* @var $generator AdGenerator|PHPUnit_Framework_MockObject_MockObject */
		$generator->setCellDelimiter(',');

		return $generator;
	}

}
