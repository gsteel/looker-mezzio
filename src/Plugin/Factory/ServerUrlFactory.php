<?php

declare(strict_types=1);

namespace Looker\Mezzio\Plugin\Factory;

use Looker\Mezzio\Plugin\ServerUrl;
use Mezzio\Helper\ServerUrlHelper;
use Psr\Container\ContainerInterface;

final class ServerUrlFactory
{
    public function __invoke(ContainerInterface $container): ServerUrl
    {
        return new ServerUrl($container->get(ServerUrlHelper::class));
    }
}
