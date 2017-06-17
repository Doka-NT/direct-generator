<?php

namespace skobka\dg\exceptions;

use Throwable;

class TitleTooLongException extends GenerateException
{
	public function __construct($message = "", $code = 0, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);

		$this->message = '[Заголовок]: ' . $this->getText();
	}
}
