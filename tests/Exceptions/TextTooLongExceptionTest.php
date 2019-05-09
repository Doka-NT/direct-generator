<?php

namespace tests\skobka\dg\Exceptions;

use PHPUnit\Framework\TestCase;
use skobka\dg\Exceptions\TooLongException;

/**
 * @coversDefaultClass \skobka\dg\Exceptions\TooLongException
 */
class TextTooLongExceptionTest extends TestCase
{
    /**
     * @return void
     */
    public function testException(): void
    {
        $exception = new TooLongException('foo bar');

        $this->assertSame('[Текст]: foo bar', $exception->getMessage());
    }
}
