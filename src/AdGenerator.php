<?php

namespace skobka\dg;

use skobka\dg\Exceptions\GenerateException;
use skobka\dg\Structures\Interfaces\LinkInterface;
use SplFileObject;

/**
 * Сервис генерации объявлений
 */
class AdGenerator
{
    /**
     * Сервис парсинга входных данных
     *
     * @var ParserInterface
     */
    private $parser;
    /**
     * Сервис формирования выходных данных
     *
     * @var View
     */
    private $view;
    /**
     * Инстанс буфера вывода
     *
     * @var SplFileObject
     */
    private $output;
    /**
     * Текстовое представление буффера вывода
     *
     * @var string
     */
    private $outputBuffer = '';
    /**
     * Флаг: наличие ошибки при работе сервиса
     *
     * @var bool
     */
    private $wasError = false;
    /**
     * Быстрые ссылки
     *
     * @var LinkInterface[]
     */
    private $quickLinks = [];

    /**
     * Генератор объявлений
     *
     * @param ParserInterface $parser Сервис парсинга входных данных
     * @param View $view Сервис формирования выходных данных
     * @param string $output буффер, куда поместить результат
     */
    public function __construct(ParserInterface $parser, View $view, string $output)
    {
        $this->parser = $parser;
        $this->view = $view;
        $this->output = new SplFileObject($output, 'w');
    }

    /**
     * Запуск генерации объявлений
     *
     * @param string $file
     */
    public function generate(string $file): void
    {
        $this->parser->parse($file);
        $this->quickLinks = $this->parser->getQuickLinks();

        foreach ($this->parser->getKeywords() as $keyword) {
            $this->processTitlesAndTexts($keyword);
        }

        if ($this->wasError) {
            return;
        }

        $this->flushOutput();
    }

    /**
     * Формирование строки с ошибкой
     *
     * @param GenerateException $exception Исключение для преобрзования в ошибку
     *
     * @return string
     */
    private function renderException(GenerateException $exception): string
    {
        $this->wasError = true;

        $message = sprintf(
            "\033[0;31m%s [%d]\033[0m\n",
            '[ERROR] ' . $exception->getMessage(),
            mb_strlen($exception->getText())
        );

        return $message;
    }

    /**
     * Вывести строку в буффер вывода
     *
     * @param string $string Строка для вывода
     */
    private function writeToOutput(string $string): void
    {
        $this->outputBuffer .= $string;
    }

    /**
     * Вывести буффер в поток output, указанный в конструкторе класса
     */
    private function flushOutput(): void
    {
        $this->output->fwrite($this->outputBuffer);
    }

    /**
     * Запустить генерацию заголовков и текстов объявлений
     *
     * @param string $keyword Ключевое слово для генерации заголовка
     */
    private function processTitlesAndTexts(string $keyword): void
    {
        foreach ($this->parser->getTitles() as $title) {
            $this->processTexts($keyword, $title);
        }
    }

    /**
     * Запустить генерацию текстов объявлений
     * При генерации тексты объявлений сразу выводятся в output указанный в конструкторе
     *
     * @param string $keyword ключевое слово
     * @param string $title заголовок
     */
    private function processTexts(string $keyword, string $title): void
    {
        foreach ($this->parser->getTexts() as $text) {
            try {
                $this->writeToOutput($this->view->renderString($keyword, $title, $text, $this->quickLinks));
            } catch (GenerateException $e) {
                echo $this->renderException($e);
            }
        }
    }
}
