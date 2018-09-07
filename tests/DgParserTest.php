<?php

namespace tests\skobka\dg;

use PHPUnit\Framework\TestCase;
use skobka\dg\DgParser;

/**
 * @coversDefaultClass \skobka\dg\DgParser
 */
class DgParserTest extends TestCase
{
    public function testGetTitles(): void
    {
        $parser = $this->getParser();
        $parser->parse();

        $this->assertSame([
            'Заголовок [key]',
            '[Key] заголовок',
        ], $parser->getTitles());
    }

    public function testGetTexts(): void
    {
        $parser = $this->getParser();
        $parser->parse();

        $this->assertSame([
            'Текст [key]',
            '[Key] текст',
        ], $parser->getTexts());
    }

    public function testGetKeywords(): void
    {
        $parser = $this->getParser();
        $parser->parse();

        $this->assertSame([
            'foo',
            'bar',
        ], $parser->getKeywords());
    }

    /**
     * @return DgParser
     */
    private function getParser(): DgParser
    {
        $file = \tempnam(\sys_get_temp_dir(), 'dg');
        \file_put_contents($file, $this->getFileContent());

        $parser = new DgParser($file);

        return $parser;
    }

    /**
     * @return string
     */
    private function getFileContent(): string
    {
        return <<<EOT
[Ключи]
foo
bar

[Заголовки]
Заголовок [key]
[Key] заголовок

[Тексты]
Текст [key]
[Key] текст

EOT;
    }
}
