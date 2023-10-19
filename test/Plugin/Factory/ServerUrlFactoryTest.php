<?php

declare(strict_types=1);

namespace Looker\Mezzio\Test\Plugin\Factory;

use Looker\Mezzio\Plugin\Factory\ServerUrlFactory;
use Looker\Mezzio\Plugin\ServerUrl;
use Mezzio\Helper\ServerUrlHelper;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ServerUrlFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with(ServerUrlHelper::class)
            ->willReturn($this->createMock(ServerUrlHelper::class));

        $plugin = (new ServerUrlFactory())($container);
        self::assertInstanceOf(ServerUrl::class, $plugin);
    }
}
