<?php

namespace skobka\dg;

/**
 * Интерфейс парсера исходного файла
 */
interface ParserInterface
{
    /**
     * Запустить парсинг
     *
     * @param string $file путь файла для парсинга
     *
     * @return void
     */
    public function parse(string $file): void;

    /**
     * Список спарсенных ключевых слов
     *
     * @return string[]
     */
    public function getKeywords(): array;

    /**
     * Список спарсенных заголовков
     *
     * @return string[]
     */
    public function getTitles(): array;

    /**
     * Список спарсенных текстов
     *
     * @return string[]
     */
    public function getTexts(): array;
}
