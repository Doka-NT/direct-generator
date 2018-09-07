<?php

namespace skobka\dg\Exceptions;

use Throwable;

class GenerateException extends \Exception
{
    protected $text;

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $this->text = $message;

        parent::__construct($message, $code, $previous);
    }

    public function getText(): string
    {
        return $this->text;
    }
}
