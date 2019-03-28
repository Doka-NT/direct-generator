<?php

namespace skobka\dg\Command;

use skobka\dg\AdGenerator;
use skobka\dg\DgParserInterface;
use skobka\dg\View;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда генерации объявлений
 */
class GenerateCommand extends Command
{
    /**
     * Опция: пропускать создание длинных заголовков
     */
    private const OPT_SKIP_LONG = 'skip-long';
    /**
     * Аргумент: исходный .dg файл
     */
    private const ARG_FILE = 'file';
    /**
     * Аргумент: файл для вывода данных
     */
    private const ARG_OUTPUT = 'output';
    /**
     * Статус код: успешно
     */
    private const CODE_OK = 0;

    /**
     * Создание инстанса команды генерации
     *
     * @param string|null $name Имя (алиас) команды для вызова
     */
    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->setName('direct-generator');
        $this->setDescription('Генерация объявлений на основе .dg файла');

        $this->addArgument(self::ARG_FILE, InputArgument::REQUIRED, 'Файл .dg для генерации объявлений');
        $this->addArgument(self::ARG_OUTPUT, InputArgument::OPTIONAL, 'Файл для сохранения результата');

        $this->addOption(
            self::OPT_SKIP_LONG,
            's',
            InputOption::VALUE_NONE,
            'Игнорировать длинные длинные значения заголовков и описаний'
        );
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputFile = $input->getArgument(self::ARG_FILE);
        $outputFile = $input->getArgument(self::ARG_OUTPUT);

        if (null === $outputFile) {
            $outputFile = \dirname($inputFile) . '/' . \basename($inputFile) . '.csv';
        }

        $parser = new DgParserInterface();
        $view = new View();
        $generator = new AdGenerator($parser, $view, $outputFile);

        $view->setSkipLong($input->getOption(self::OPT_SKIP_LONG));

        if (pathinfo($outputFile, PATHINFO_EXTENSION) === 'csv') {
            $view->setCellDelimiter(',');
        }

        $generator->generate($inputFile);

        return self::CODE_OK;
    }
}
