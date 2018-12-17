<?php

namespace skobka\dg;

use skobka\dg\Exceptions\TextTooLongException;
use skobka\dg\Exceptions\TitleTooLongException;

class View
{
    private const KEYWORD_MARKER = '[key]';
    private const KEYWORD_MARKER_CAPITALIZE = '[Key]';
    private const MAX_ADS_IN_GROUP = 50;
    private const MAX_TITLE_LENGTH = 33;
    private const MAX_TEXT_LENGTH = 75;

    /**
     * @var int
     */
    private $groupNum = 1;
    /**
     * @var int
     */
    private $adCounter = 0;
    /**
     * @var string
     */
    private $cellDelimiter = "\t\t";
    /**
     * @var string
     */
    private $rowDelimiter = "\n";
    /**
     * @var bool
     */
    private $skipLong = false;

    /**
     * Формирование строки для вывода
     *
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
        $this->updateAdCounter();

        $title = $this->replaceKeys($title, $keyword);
        $text = $this->replaceKeys($text, $keyword);

        if (!$this->skipLong && mb_strlen($title) > self::MAX_TITLE_LENGTH) {
            throw new TitleTooLongException($title);
        }

        if (mb_strlen($text) > self::MAX_TEXT_LENGTH && !$this->skipLong) {
            throw new TextTooLongException($text);
        }

        return implode($this->cellDelimiter, [
                '-',
                'Текстово-графическое',
                '-',
                '',
                "$keyword {$this->groupNum}",
                '',
                '-',
                '',
                $keyword,
                '', //продуктивность
                '',
                $title,
                '',
                $text,
            ]) . $this->rowDelimiter;
    }

    /**
     * Обновление счетчика объявлений
     */
    private function updateAdCounter(): void
    {
        if ($this->adCounter && $this->adCounter % self::MAX_ADS_IN_GROUP === 0) {
            $this->groupNum++;
        }
        $this->adCounter++;
    }

    /**
     * Замена ключей в строке
     *
     * @param string $input
     * @param string $replacement
     *
     * @return string
     */
    private function replaceKeys(string $input, string $replacement): string
    {
        $replacement = (string)preg_replace('/\s\+[а-яА-Я]+|\s\-[а-яА-Я]+/u', '', $replacement);

        $result = str_replace([self::KEYWORD_MARKER_CAPITALIZE, self::KEYWORD_MARKER], [
            mb_convert_case($replacement, MB_CASE_TITLE, 'UTF-8'),
            mb_convert_case($replacement, MB_CASE_LOWER, 'UTF-8')
        ], $input);

        return $result;
    }

    /**
     * Разрешить пропуск длинных заголовков
     * и текстов без выбрасывания исключений
     *
     * @param bool $skip
     */
    public function setSkipLong(bool $skip)
    {
        $this->skipLong = $skip;
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
}
