<?php

namespace skobka\dg\Exceptions;

use Throwable;

/**
 * Исключение: Текст слишком длинный
 */
class TextTooLongException extends GenerateException
{
    /**
     * @inheritdoc
     */
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->message = '[Текст]: ' . $this->getText();
    }
}
