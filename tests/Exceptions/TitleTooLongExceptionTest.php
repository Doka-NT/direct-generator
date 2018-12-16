<?php

namespace tests\skobka\dg\Exceptions;

use PHPUnit\Framework\TestCase;
use skobka\dg\Exceptions\TitleTooLongException;

/**
 * @coversDefaultClass \skobka\dg\Exceptions\TitleTooLongException
 */
class TitleTooLongExceptionTest extends TestCase
{
    /**
     * @return void
     */
    public function testException()
    {
        $exception = new TitleTooLongException('foo bar');

        $this->assertSame('[Заголовок]: foo bar', $exception->getMessage());
    }
}
