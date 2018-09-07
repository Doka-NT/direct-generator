<?php

namespace tests\skobka\dg\Command;

use PHPUnit\Framework\TestCase;
use skobka\dg\Command\GenerateCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @coversDefaultClass \skobka\dg\Command\GenerateCommand
 */
class GenerateCommandTest extends TestCase
{
    /**
     * @covers ::__construct()
     */
    public function testConstruct(): void
    {
        $command = new GenerateCommand();

        $this->assertSame('direct-generator', $command->getName());
        $this->assertSame('Генерация объявлений на основе .dg файла', $command->getDescription());

        $fileArgument = $command->getDefinition()->getArgument('file');
        $this->assertSame('Файл .dg для генерации объявлений', $fileArgument->getDescription());
        $this->assertTrue($fileArgument->isRequired());

        $outputArgument = $command->getDefinition()->getArgument('output');
        $this->assertSame('Файл для сохранения результата', $outputArgument->getDescription());
        $this->assertSame('php://stdout', $outputArgument->getDefault());
        $this->assertFalse($outputArgument->isRequired());

        $skipLongOption = $command->getDefinition()->getOption('skip-long');
        $this->assertSame('s', $skipLongOption->getShortcut());
        $this->assertSame(
            'Игнорировать длинные длинные значения заголовков и описаний',
            $skipLongOption->getDescription()
        );
        $this->assertFalse($skipLongOption->acceptValue());
    }

    /**
     * @covers ::execute()
     * @throws \Exception
     */
    public function testExecuteEmptyOrBadInput(): void
    {
        $content = 'foo_bar';
        [$outputFile, $input, $output] = $this->getMocks($content);

        $command = new GenerateCommand();
        $exitCode = $command->execute($input, $output);

        $this->assertSame(0, $exitCode);
        $this->assertSame('', \file_get_contents($outputFile));
    }

    /**
     * @covers ::execute()
     * @return void
     * @throws \Exception
     */
    public function testExecuteSimpleOutput(): void
    {
        $content = '
[Ключи]
foobar

[Заголовки]
Title 1 [key]

[Тексты]
[Key] text 1
';
        [$outputFile, $input, $output] = $this->getMocks($content);

        $command = new GenerateCommand();
        $exitCode = $command->execute($input, $output);

        $this->assertSame(0, $exitCode);
        $this->assertSame(
            "foobar\t\tTitle 1 foobar\t\tFoobar text 1\n",
            \file_get_contents($outputFile)
        );
    }

    /**
     * @covers ::execute()
     * @return void
     * @throws \Exception
     */
    public function testExecuteCsvOutput(): void
    {
        $content = '
[Ключи]
foobar

[Заголовки]
Title 1 [key]

[Тексты]
[Key] text 1
';
        [$outputFile, $input, $output] = $this->getMocks($content, 'csv');

        $command = new GenerateCommand();
        $exitCode = $command->execute($input, $output);

        $this->assertSame(0, $exitCode);
        $this->assertSame(
            "foobar,Title 1 foobar,Foobar text 1\n",
            \file_get_contents($outputFile)
        );
    }

    /**
     * @covers ::execute()
     * @return void
     * @throws \Exception
     */
    public function testExecuteWithLongTitle(): void
    {
        $content = '
[Ключи]
foobarbaz_and_very_very_long_title_to_throw_exception

[Заголовки]
Title 1 [key]

[Тексты]
[Key] text 1
';
        $length = \mb_strlen('Title 1 foobarbaz_and_very_very_long_title_to_throw_exception');

        [, $input, $output] = $this->getMocks($content, 'csv', false);

        $command = new GenerateCommand();

        \ob_start();
        $command->execute($input, $output);
        $out = \ob_get_clean();

        $this->assertContains(
            "[ERROR] [Заголовок]: Title 1 foobarbaz_and_very_very_long_title_to_throw_exception [$length]",
            $out
        );
    }

    /**
     * @param $content
     * @param string $extension
     * @return bool|string
     */
    private function createTempFile($content, string $extension = '')
    {
        $name = \sys_get_temp_dir() . '/' . \uniqid('dg-') . ($extension ? '.' . $extension : '');
        \file_put_contents($name, $content);

        return $name;
    }

    /**
     * @param string $inputFile
     * @param string $outputFile
     * @param bool $skip
     * @return \PHPUnit_Framework_MockObject_MockObject|InputInterface
     */
    private function getInputMock(string $inputFile, string $outputFile, bool $skip)
    {
        /* @var $input InputInterface|\PHPUnit_Framework_MockObject_MockObject */
        $input = $this
            ->getMockBuilder(InputInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getArgument', 'getOption'])
            ->getMockForAbstractClass();
        $input->method('getOption')->with('skip-long')->willReturn($skip);

        $input
            ->expects($this->exactly(2))
            ->method('getArgument')
            ->willReturnMap([
                ['file', $inputFile],
                ['output', $outputFile],
            ]);

        return $input;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|OutputInterface
     */
    private function getOutputMock()
    {
        /* @var $output OutputInterface|\PHPUnit_Framework_MockObject_MockObject */
        $output = $this
            ->getMockBuilder(OutputInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        return $output;
    }

    /**
     * @param string $content
     * @param string $outFormat
     * @param bool $skip
     * @return array
     */
    private function getMocks(string $content, string $outFormat = '', bool $skip = true): array
    {
        $inputFile = $this->createTempFile($content);
        $outputFile = $this->createTempFile('', $outFormat);
        $input = $this->getInputMock($inputFile, $outputFile, $skip);
        $output = $this->getOutputMock();

        return array($outputFile, $input, $output);
    }
}
