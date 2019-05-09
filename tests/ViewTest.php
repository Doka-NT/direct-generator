<?php

namespace tests\skobka\dg;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionProperty;
use skobka\dg\Exceptions\TitleTooLongException;
use skobka\dg\Exceptions\TooLongException;
use skobka\dg\View;
use function uniqid;

/**
 * @coversDefaultClass \skobka\dg\View
 */
class ViewTest extends TestCase
{
    /**
     * @return array
     */
    public function boolDataProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @dataProvider boolDataProvider
     * @param bool $skipLong
     * @return void
     * @throws ReflectionException
     */
    public function testSkipLong(bool $skipLong): void
    {
        $view = new View();
        $view->setSkipLong($skipLong);

        $skipLongProperty = new ReflectionProperty(View::class, 'skipLong');
        $skipLongProperty->setAccessible(true);

        $this->assertSame($skipLong, $skipLongProperty->getValue($view));
    }


    /**
     * @return void
     * @throws ReflectionException
     */
    public function testSetCellDelimiter(): void
    {
        $value = uniqid('foo', false);

        $view = new View();

        $view->setCellDelimiter($value);

        $property = new ReflectionProperty(View::class, 'cellDelimiter');
        $property->setAccessible(true);

        $this->assertSame($value, $property->getValue($view));
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testSetRowDelimiter(): void
    {
        $value = uniqid('foo', false);
        $view = new View();

        $view->setRowDelimiter($value);

        $property = new ReflectionProperty(View::class, 'rowDelimiter');
        $property->setAccessible(true);

        $this->assertSame($value, $property->getValue($view));
    }

    /**
     * @return void
     * @throws TitleTooLongException
     * @throws TooLongException
     */
    public function testTitleTooLong(): void
    {
        $view = new View();

        $this->expectException(TitleTooLongException::class);
        $this->expectExceptionMessage('[Заголовок]: some very long title with very long text');
        $view->renderString('foo', 'some very long title with very long text', 'bar');
    }

    /**
     * @return void
     * @throws TooLongException
     * @throws TitleTooLongException
     */
    public function testTextTooLong(): void
    {
        $view = new View();

        $this->expectException(TooLongException::class);
        $this->expectExceptionMessage(
            '[Текст]: some very very long text with very long sentense, containing a lot of letters'
        );
        $view->renderString(
            'foo',
            'bar',
            'some very very long text with very long sentense, containing a lot of letters'
        );
    }

    /**
     * @return void
     * @throws TooLongException
     * @throws TitleTooLongException
     */
    public function testUpdateAdCounter(): void
    {
        $view = new View();
        $lastString = '';
        for ($i = 0; $i <= 50; $i++) {
            $lastString = $view->renderString('foo', 'bar', 'baz');
        }
        $this->assertContains('foo 2', $lastString);
    }
}
