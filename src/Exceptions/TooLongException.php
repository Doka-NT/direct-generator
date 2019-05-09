<?php

namespace skobka\dg\Exceptions;

use Throwable;

/**
 * Исключение: Текст слишком длинный
 */
class TooLongException extends GenerateException
{
    /**
     * Тип: превышена допустимая длина текста
     */
    public const TYPE_TEXT = 1;
    /**
     * Тип: превышена допустимая длина заголовка
     */
    public const TYPE_TITLE = 2;
    /**
     * Тип: превышена допустимая длина быстрых ссылок
     */
    public const TYPE_QUICK_LINKS = 3;

    /**
     * Карта названий типов исключений
     */
    private const TYPE_NAMES = [
        self::TYPE_TEXT => 'Текст',
        self::TYPE_TITLE => 'Заголовок',
        self::TYPE_QUICK_LINKS => 'Быстрые ссылки',
    ];

    /**
     * @inheritdoc
     */
    public function __construct(int $type, string $message, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->message = sprintf('[%s]: %s', self::TYPE_NAMES[$type] ?? 'unclassified', $this->getText());
    }
}
