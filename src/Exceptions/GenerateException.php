<?php

namespace skobka\dg\Exceptions;

use Throwable;

/**
 * Исключение при генерации объявлений
 */
class GenerateException extends \Exception
{
    /**
     * @var string Текст описания исключительной ситуации
     */
    protected $text;

    /**
     * @inheritdoc
     */
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        $this->text = $message;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Текст описания исключительной ситуации
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
