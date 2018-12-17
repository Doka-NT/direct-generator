<?php

namespace skobka\dg;

use skobka\dg\Exceptions\GenerateException;

class AdGenerator
{
    /**
     * @var Parser
     */
    private $parser;
    /**
     * @var View
     */
    private $view;
    /**
     * @var \SplFileObject
     */
    private $output;
    /**
     * @var string
     */
    private $outputBuffer = '';
    /**
     * @var bool
     */
    private $wasError = false;

    /**
     * Генератор объявлений
     *
     * @param Parser $parser
     * @param View $view
     * @param string $output
     */
    public function __construct(Parser $parser, View $view, string $output)
    {
        $this->parser = $parser;
        $this->view = $view;
        $this->output = new \SplFileObject($output, 'w');
    }

    /**
     * Генерирует наборы объявлений
     *
     * @param string $file
     */
    public function generate(string $file): void
    {
        $this->parser->parse($file);

        foreach ($this->parser->getKeywords() as $keyword) {
            $this->processTitles($keyword);
        }

        if ($this->wasError) {
            return;
        }

        $this->flushOutput();
    }

    /**
     * Формирование строки с ошибкой
     *
     * @param GenerateException $exception
     * @return string
     */
    private function renderException(GenerateException $exception)
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
     * @param string $string
     */
    private function writeToOutput(string $string)
    {
        $this->outputBuffer .= $string;
    }

    /**
     * Вывод буффера в output
     */
    private function flushOutput()
    {
        $this->output->fwrite($this->outputBuffer);
    }

    /**
     * @param string $keyword
     */
    private function processTitles(string $keyword)
    {
        foreach ($this->parser->getTitles() as $title) {
            $this->processTexts($keyword, $title);
        }
    }

    /**
     * @param string $keyword
     * @param string $title
     */
    private function processTexts(string $keyword, string $title)
    {
        foreach ($this->parser->getTexts() as $text) {
            try {
                $this->writeToOutput($this->view->renderString($keyword, $title, $text));
            } catch (GenerateException $e) {
                echo $this->renderException($e);
            }
        }
    }
}
