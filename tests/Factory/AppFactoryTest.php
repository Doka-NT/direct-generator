<?php

namespace tests\skobka\dg\Factory;

use PHPUnit\Framework\TestCase;
use skobka\dg\Factory\AppFactory;
use function array_keys;

/**
 * @coversDefaultClass \skobka\dg\Factory\AppFactory
 */
class AppFactoryTest extends TestCase
{

    /**
     * @covers ::create()
     */
    public function testCreate()
    {
        $app = AppFactory::create();

        $this->assertSame('direct-generator', $app->getName());
        $this->assertSame('1.0.0', $app->getVersion());

        $commands = $app->all();
        $this->assertCount(3, $commands);
        $this->assertSame(['help', 'list', 'direct-generator'], array_keys($commands));
    }
}
