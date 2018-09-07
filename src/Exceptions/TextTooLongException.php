<?php

namespace skobka\dg\Exceptions;

use Throwable;

class TextTooLongException extends GenerateException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->message = '[Текст]: ' . $this->getText();
    }
}
