<?php

namespace skobka\dg\Parsers\DG;

use skobka\dg\Structures\Link;

/**
 * Парсер строки с описанием быстрой ссылки
 */
class LinkParser
{
    /**
     * Символ-разделитель информации о ссылке
     */
    public const QUICK_LINK_INFO_DELIMITER = '||';

    /**
     * Парсинг строки с описанием быстрой ссылки
     *
     * @param string $line строка описанием
     * @return Link Структура ссылки
     */
    public static function parse(string $line): Link
    {
        $parts = array_filter(explode(self::QUICK_LINK_INFO_DELIMITER, $line));
        $parts = array_map('trim', $parts);

        return new Link($parts[1] ?? '', $parts[0] ?? '');
    }
}
