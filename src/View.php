<?php

namespace skobka\dg;

use skobka\dg\Exceptions\GenerateException;
use skobka\dg\Exceptions\TextTooLongException;
use skobka\dg\Exceptions\TitleTooLongException;

/**
 * Сервис формирования вывода
 */
class View
{
    /**
     * Маркер. Подстановочный шаблон ключевого слова
     */
    private const KEYWORD_MARKER = '[key]';
    /**
     * Маркер. Подстановочный шаблон ключевого слова с большой буквы
     */
    private const KEYWORD_MARKER_CAPITALIZE = '[Key]';
    /**
     * Максимальное количество объявлений в группе
     */
    private const MAX_ADS_IN_GROUP = 50;
    /**
     * Максимальная длина заголовка
     */
    private const MAX_TITLE_LENGTH = 33;
    /**
     * Максимальная длина текста
     */
    private const MAX_TEXT_LENGTH = 75;

    /**
     * Текущий номер группы
     *
     * @var int
     */
    private $groupNum = 1;
    /**
     * Счетчик объявлений
     *
     * @var int
     */
    private $adCounter = 0;
    /**
     * Разделитель ячейки при формировании вывода
     *
     * @var string
     */
    private $cellDelimiter = "\t\t";
    /**
     * Разделитель строки при формировании вывода
     *
     * @var string
     */
    private $rowDelimiter = "\n";
    /**
     * Флаг: пропускать длинные заголовки и текст
     * Если пропуск запрещен, при получении длинного заголовка или текста будет выброшено исключение
     *
     * @var bool
     *
     * @see GenerateException
     */
    private $skipLong = false;

    /**
     * Формирование строки для вывода
     *
     * @param string $keyword ключевое слово
     * @param string $title заголовок объявления
     * @param string $text текст объявления
     *
     * @return string строка для вывода
     *
     * @throws TextTooLongException выбрасывается, когда текст превышает MAX_TEXT_LENGTH
     * @throws TitleTooLongException выбрасывается, когда заголовок превышает MAX_TITLE_LENGTH
     *
     * @see View::MAX_TITLE_LENGTH
     * @see View::MAX_TEXT_LENGTH
     */
    public function renderString(string $keyword, string $title, string $text): string
    {
        $this->updateAdCounter();

        $title = $this->replaceKeys($title, $keyword);
        $text = $this->replaceKeys($text, $keyword);

        if (!$this->skipLong && mb_strlen($title) > self::MAX_TITLE_LENGTH) {
            throw new TitleTooLongException($title);
        }

        if (!$this->skipLong && mb_strlen($text) > self::MAX_TEXT_LENGTH) {
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
     * @param string $input строка, в которой требуется провести замену
     * @param string $replacement строка на которую производится замена
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
    public function setSkipLong(bool $skip): void
    {
        $this->skipLong = $skip;
    }

    /**
     * Установка разделителя ячейки
     *
     * @param string $cellDelimiter
     */
    public function setCellDelimiter(string $cellDelimiter): void
    {
        $this->cellDelimiter = $cellDelimiter;
    }

    /**
     * Установка разделителя строк
     *
     * @param string $rowDelimiter
     */
    public function setRowDelimiter(string $rowDelimiter): void
    {
        $this->rowDelimiter = $rowDelimiter;
    }
}
