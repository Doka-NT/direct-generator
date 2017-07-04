<?php

namespace skobka\dg;

use skobka\dg\exceptions\GenerateException;
use skobka\dg\exceptions\TextTooLongException;
use skobka\dg\exceptions\TitleTooLongException;

class AdGenerator
{
    const KEYWORD_MARKER = '[key]';

    /**
     * @var Parser
     */
    private $parser;
    /**
     * @var \SplFileObject
     */
    private $output;
    /**
     * @var string
     */
    private $cellDelimiter = "\t\t";
    /**
     * @var string
     */
    private $rowDelimiter = "\n";
    /**
     * @var string
     */
    private $outputBuffer = '';
    /**
     * @var bool
     */
    private $wasError = false;
    /**
     * @var bool
     */
    private $skipLong = false;

    /**
     * AdGenerator constructor.
     *
     * @param Parser $parser
     * @param string $output
     */
    public function __construct(Parser $parser, string $output)
    {
        $this->parser = $parser;
        $this->output = new \SplFileObject($output, 'w');

        $this->parser->parse();
    }

    /**
     * Генерирует наборы объявлений
     */
    public function generate()
    {
        foreach ($this->parser->getKeywords() as $keyword) {
            foreach ($this->parser->getTitles() as $title) {
                foreach ($this->parser->getTexts() as $text) {
                    try {
                        $this->writeToOutput($this->renderString($keyword, $title, $text));
                    } catch (GenerateException $e) {
                        echo $this->renderException($e);
                    }
                }
            }
        }

        if ($this->wasError) {
            return;
        }

        $this->flushOutput();
    }

    /**
     * @param string $cellDelimiter
     */
    public function setCellDelimiter(string $cellDelimiter)
    {
        $this->cellDelimiter = $cellDelimiter;
    }

    /**
     * @param string $rowDelimiter
     */
    public function setRowDelimiter(string $rowDelimiter)
    {
        $this->rowDelimiter = $rowDelimiter;
    }

    /**
     * Формирование строки для вывода
     * @param string $keyword
     * @param string $title
     * @param string $text
     *
     * @return string
     * @throws TextTooLongException
     * @throws TitleTooLongException
     */
    public function renderString(string $keyword, string $title, string $text): string
    {
        $title = $this->replaceKeys($title, $keyword);
        $text = $this->replaceKeys($text, $keyword);

        if (mb_strlen($title) > 33 && !$this->skipLong) {
            throw new TitleTooLongException($title);
        }

        if (mb_strlen($text) > 75 && !$this->skipLong) {
            throw new TextTooLongException($text);
        }

        return sprintf(
            "%s%s%s%s%s%s",
            $keyword,
            $this->cellDelimiter,
            $title,
            $this->cellDelimiter,
            $text,
            $this->rowDelimiter
        );
    }

    /**
     * Разрешить пропуск длинных заголовков
     * и текстов без выбрасывания исключений
     * @param bool $skip
     */
    public function setSkipLong(bool $skip)
    {
        $this->skipLong = $skip;
    }

    /**
     * Формирование строки с ошибкой
     * @param GenerateException $e
     * @return string
     */
    protected function renderException(GenerateException $e)
    {
        $this->wasError = true;

        $message = sprintf(
            "\033[0;31m%s [%d]\033[0m\n",
            '[ERROR] ' . $e->getMessage(),
            mb_strlen($e->getText())
        );

        return $message;
    }

    /**
     * @param string $string
     */
    protected function writeToOutput(string $string)
    {
        $this->outputBuffer .= $string;
    }

    /**
     *
     */
    protected function flushOutput()
    {
        $this->output->fwrite($this->outputBuffer);
    }


    /**
     * Замена ключей в строке
     * @param string $input
     * @param string $replacement
     *
     * @return string
     */
    private function replaceKeys(string $input, string $replacement): string
    {

        $replacement = preg_replace('/\s\+[а-яА-Я]+|\s\-[а-яА-Я]+/u', '', $replacement);

        $result = str_replace('[Key]', mb_convert_case($replacement, MB_CASE_TITLE, 'UTF-8'), $input);
        $result = str_replace('[key]', mb_convert_case($replacement, MB_CASE_LOWER, 'UTF-8'), $result);

        return $result;
    }
}
