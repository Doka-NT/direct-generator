<?php

namespace tests\skobka\dg;

use PHPUnit\Framework\TestCase;
use skobka\dg\Parsers\DG\FileParser;
use skobka\dg\Structures\Link;
use function file_put_contents;
use function sys_get_temp_dir;
use function tempnam;

/**
 * @coversDefaultClass \skobka\dg\Parsers\DG\FileParser
 */
class DgParserTest extends TestCase
{
    public function testGetTitles(): void
    {
        $parser = new FileParser();
        $parser->parse($this->getFile());

        $this->assertSame([
            'Заголовок [key]',
            '[Key] заголовок',
        ], $parser->getTitles());
    }

    public function testGetTexts(): void
    {
        $parser = new FileParser();
        $parser->parse($this->getFile());

        $this->assertSame([
            'Текст [key]',
            '[Key] текст',
        ], $parser->getTexts());
    }

    public function testGetKeywords(): void
    {
        $parser = new FileParser();
        $parser->parse($this->getFile());

        $this->assertSame([
            'foo',
            'bar',
        ], $parser->getKeywords());
    }

    /**
     * Тест парсинга быстрых ссылок
     */
    public function testGetQuickLinks(): void
    {
        $parser = new FileParser();
        $parser->parse($this->getFile());

        $this->assertEquals([
            new Link('https://example.com', 'Ссылка 1'),
            new Link('https://foo.example2.com', 'Ссылка 2'),
        ], $parser->getQuickLinks());
    }

    /**
     * @return string
     */
    private function getFile(): string
    {
        $file = tempnam(sys_get_temp_dir(), 'dg');
        file_put_contents($file, $this->getFileContent());

        return $file;
    }

    /**
     * @return string
     */
    private function getFileContent(): string
    {
        return <<<EOT
not used        
        
[Быстрые ссылки]
Ссылка 1 || https://example.com
Ссылка 2 || https://foo.example2.com
        
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
