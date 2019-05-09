<?php

namespace tests\skobka\dg\Exceptions;

use PHPUnit\Framework\TestCase;
use skobka\dg\Exceptions\TooLongException;

/**
 * @coversDefaultClass \skobka\dg\Exceptions\TooLongException
 */
class TooLongExceptionTest extends TestCase
{
    /**
     * @return void
     */
    public static function testTextException(): void
    {
        $exception = new TooLongException(TooLongException::TYPE_TEXT, 'foo bar');

        self::assertSame('[Текст]: foo bar', $exception->getMessage());
    }

    /**
     * @return void
     */
    public static function testTitleException(): void
    {
        $exception = new TooLongException(TooLongException::TYPE_TITLE, 'foo bar');

        self::assertSame('[Заголовок]: foo bar', $exception->getMessage());
    }
}
