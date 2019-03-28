<?php

namespace tests\skobka\dg;

use PHPUnit\Framework\TestCase;
use skobka\dg\DgParserInterface;

/**
 * @coversDefaultClass \skobka\dg\DgParserInterface
 */
class DgParserTest extends TestCase
{
    public function testGetTitles(): void
    {
        $parser = new DgParserInterface();
        $parser->parse($this->getFile());

        $this->assertSame([
            'Заголовок [key]',
            '[Key] заголовок',
        ], $parser->getTitles());
    }

    public function testGetTexts(): void
    {
        $parser = new DgParserInterface();
        $parser->parse($this->getFile());

        $this->assertSame([
            'Текст [key]',
            '[Key] текст',
        ], $parser->getTexts());
    }

    public function testGetKeywords(): void
    {
        $parser = new DgParserInterface();
        $parser->parse($this->getFile());

        $this->assertSame([
            'foo',
            'bar',
        ], $parser->getKeywords());
    }

    /**
     * @return string
     */
    private function getFile(): string
    {
        $file = \tempnam(\sys_get_temp_dir(), 'dg');
        \file_put_contents($file, $this->getFileContent());

        return $file;
    }

    /**
     * @return string
     */
    private function getFileContent(): string
    {
        return <<<EOT
not used        
        
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
