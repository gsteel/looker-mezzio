<?php

declare(strict_types=1);

namespace Looker\Mezzio\Test\Plugin;

use Looker\Mezzio\Plugin\Url;
use Mezzio\Helper\UrlHelperInterface;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public function testThatParametersArePassedToTheComposedUrlHelper(): void
    {
        $helper = $this->createMock(UrlHelperInterface::class);
        $plugin = new Url($helper);

        $helper->expects(self::once())
            ->method('generate')
            ->with(
                self::identicalTo('some-route'),
                self::identicalTo(['foo' => 'bar']),
                self::identicalTo(['baz' => 'bat']),
                self::identicalTo('some-id'),
                self::identicalTo(['reuse_result_params' => true]),
            )->willReturn('/blah');

        $url = $plugin->__invoke(
            'some-route',
            ['foo' => 'bar'],
            ['baz' => 'bat'],
            'some-id',
            ['reuse_result_params' => true],
        );

        self::assertSame('/blah', $url);
    }
}
