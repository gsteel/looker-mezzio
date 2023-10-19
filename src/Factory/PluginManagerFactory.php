<?php

declare(strict_types=1);

namespace Looker\Mezzio\Factory;

use GSteel\Dot;
use Laminas\ServiceManager\ServiceManager;
use Looker\Mezzio\PluginManager as MezzioPluginManager;
use Looker\PluginManager;
use Looker\Renderer\PluginProxy;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

/** @psalm-import-type ServiceManagerConfiguration from ServiceManager */
final class PluginManagerFactory
{
    public function __invoke(ContainerInterface $container): PluginManager
    {
        $config = $container->has('config')
            ? $container->get('config')
            : [];
        Assert::isArray($config);

        /** @psalm-var ServiceManagerConfiguration $pluginConfig */
        $pluginConfig = Dot::array('looker.plugins', $config);

        return new PluginProxy(new MezzioPluginManager($container, $pluginConfig));
    }
}
