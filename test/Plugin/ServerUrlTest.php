<?php

declare(strict_types=1);

namespace Looker\Mezzio\Test\Plugin;

use Looker\Mezzio\Plugin\ServerUrl;
use Mezzio\Helper\ServerUrlHelper;
use PHPUnit\Framework\TestCase;

class ServerUrlTest extends TestCase
{
    public function testThatParametersWillBePassedToTheUnderlyingHelper(): void
    {
        $helper = $this->createMock(ServerUrlHelper::class);
        $plugin = new ServerUrl($helper);
        $helper->expects(self::once())
            ->method('generate')
            ->with('/foo')
            ->willReturn('blah');

        self::assertSame('blah', $plugin->__invoke('/foo'));
    }
}
