<?php

namespace skobka\dg\Structures\Interfaces;

/**
 * Структура данных. Ссылка
 */
interface LinkInterface
{
    /**
     * URL адрес ссылки
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Текст ссылки
     *
     * @return string
     */
    public function getText(): string;
}
