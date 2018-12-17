<?php

namespace tests\skobka\dg\Exceptions;

use PHPUnit\Framework\TestCase;
use skobka\dg\Exceptions\TextTooLongException;

/**
 * @coversDefaultClass \skobka\dg\Exceptions\TextTooLongException
 */
class TextTooLongExceptionTest extends TestCase
{
    /**
     * @return void
     */
    public function testException()
    {
        $exception = new TextTooLongException('foo bar');

        $this->assertSame('[Текст]: foo bar', $exception->getMessage());
    }
}
