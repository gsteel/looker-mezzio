<?php

declare(strict_types=1);

namespace Looker\Mezzio\Plugin\Factory;

use Looker\Mezzio\Plugin\Url;
use Mezzio\Helper\UrlHelperInterface;
use Psr\Container\ContainerInterface;

final class UrlFactory
{
    public function __invoke(ContainerInterface $container): Url
    {
        return new Url($container->get(UrlHelperInterface::class));
    }
}
