<?php

namespace skobka\dg\Structures;

use skobka\dg\Structures\Interfaces\LinkInterface;

/**
 * Структура данных. Ссылка
 */
class Link implements LinkInterface
{
    /**
     * URL адрес ссылки
     *
     * @var string
     */
    private $url;
    /**
     * Текст ссылки
     *
     * @var string
     */
    private $text;

    /**
     * Структура данных. Ссылка
     *
     * @param string $url URL адрес ссылки
     * @param string $text Текст ссылки
     */
    public function __construct(string $url, string $text)
    {
        $this->url = $url;
        $this->text = $text;
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return $this->text;
    }
}
