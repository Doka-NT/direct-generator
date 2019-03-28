<?php

namespace skobka\dg;

use SplFileObject;

/**
 * Парсер .dg файлов
 * .dg файл содержит следующую структуру
 * <code>
 * [Ключи]
 * ключевые слова
 * каждог с новой строки
 *
 * [Заголовки]
 * Текст заголовка с указанием ключа как [key]
 * [Key] если указать ключ с большой буквы, то он будет подставлен с заглавной
 *
 * [Тексты]
 * Формат аналогичен секции Заголовков
 * </code>
 */
class DgParser implements ParserInterface
{
    /**
     * Флаг: Секция - ключевые слова
     */
    public const FLAG_KEYWORDS = 1;
    /**
     * Флаг: Секция - заголовки
     */
    public const FLAG_TITLES = 2;
    /**
     * Флаг: Секция - тексты
     */
    public const FLAG_TEXTS = 3;

    /**
     * Маркер секции - ключевые слова
     */
    public const MARKER_KEYWORDS = '[Ключи]';
    /**
     * Маркер секции - заголовки
     */
    public const MARKER_TITLES = '[Заголовки]';
    /**
     * Макрер секции - тексты
     */
    public const MARKER_TEXTS = '[Тексты]';

    /**
     * Массив флагов для поиска
     *
     * @var int[]
     */
    private const FLAGS = [self::FLAG_KEYWORDS, self::FLAG_TITLES, self::FLAG_TEXTS];
    /**
     * Массив маркеров для поиска
     */
    private const MARKERS = [self::MARKER_KEYWORDS, self::MARKER_TITLES, self::MARKER_TEXTS];

    /**
     * Инстанс файла для парсинга
     *
     * @var \SplFileObject
     */
    private $file;
    /**
     * Спарсенные ключевые слова
     *
     * @var string[]
     */
    private $keywords = [];
    /**
     * Спарсенные заголовки
     *
     * @var string[]
     */
    private $titles = [];
    /**
     * Спарсенные тексты
     *
     * @var string[]
     */
    private $texts = [];
    /**
     * Флаг. Текущая секция
     *
     * @var int
     */
    private $flagCurrentSection = 0;

    /**
     * @inheritdoc
     */
    public function parse(string $file): void
    {
        $this->createFileInstance($file);

        while (!$this->file->eof()) {
            $this->parseLine(
                trim($this->file->fgets())
            );
        }

        $this->filterData();
    }

    /**
     * @inheritdoc
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * @inheritdoc
     */
    public function getTitles(): array
    {
        return $this->titles;
    }

    /**
     * @inheritdoc
     */
    public function getTexts(): array
    {
        return $this->texts;
    }

    /**
     * Добавить найденные текст в набор в зависимости от флага
     *
     * @param string $text спарсенный текст
     * @param int $flag флаг типа текста
     */
    private function addText(string $text, int $flag): void
    {
        if ($flag === static::FLAG_KEYWORDS) {
            $this->keywords[] = $text;
        } elseif ($flag === static::FLAG_TITLES) {
            $this->titles[] = $text;
        } elseif ($flag === static::FLAG_TEXTS) {
            $this->texts[] = $text;
        }
    }

    /**
     * Создание инстанса файла из его пути
     *
     * @param string $file
     */
    private function createFileInstance(string $file): void
    {
        $this->file = new SplFileObject($file);
    }

    /**
     * Очистка спарсенных данных от пустых значений
     */
    private function filterData(): void
    {
        $this->keywords = array_filter($this->keywords);
        $this->titles = array_filter($this->titles);
        $this->texts = array_filter($this->texts);
    }

    /**
     * Парсинг строки из исходного файла
     *
     * @param string $text строка текста из исходного файла
     */
    private function parseLine(string $text): void
    {
        if ($this->checkIsSection($text)) {
            return;
        }

        $this->addText($text, $this->flagCurrentSection);
    }

    /**
     * Проверка является ли $line ключевым словом определяющим секцию файлы
     *
     * @param string $line строка текста из исходного файла
     *
     * @return bool
     */
    private function checkIsSection(string $line): bool
    {
        $key = array_search($line, static::MARKERS, true);

        if ($key === false) {
            return false;
        }

        $this->flagCurrentSection = static::FLAGS[$key] ?? 0;

        return true;
    }
}
