<?php

namespace tests\skobka\dg;

use skobka\dg\Input;
use PHPUnit\Framework\TestCase;

class InputTest extends TestCase
{
    /**
     * Тест получения опции --skip-long
     */
    public function testHasSkipLongOption()
    {
        $_SERVER['argv'][] = '--skip-long';

        $result = Input::getInstance()->hasSkipLong();

        $this->assertTrue($result);
    }
}
