<?php

declare(strict_types=1);

namespace Looker\Mezzio\Test\Plugin\Factory;

use Looker\Mezzio\Plugin\Factory\UrlFactory;
use Looker\Mezzio\Plugin\Url;
use Mezzio\Helper\UrlHelperInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class UrlFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with(UrlHelperInterface::class)
            ->willReturn($this->createMock(UrlHelperInterface::class));

        $plugin = (new UrlFactory())($container);
        self::assertInstanceOf(Url::class, $plugin);
    }
}
