<?php

declare(strict_types=1);

namespace Looker\Mezzio\Factory;

use Laminas\ServiceManager\ServiceManager;
use Looker\Mezzio\PluginManager as MezzioPluginManager;
use Looker\PluginManager;
use Looker\Renderer\PluginProxy;
use Psr\Container\ContainerInterface;

use function Psl\Type\dict;
use function Psl\Type\mixed;
use function Psl\Type\shape;
use function Psl\Type\string;

/** @psalm-import-type ServiceManagerConfiguration from ServiceManager */
final class PluginManagerFactory
{
    public function __invoke(ContainerInterface $container): PluginManager
    {
        $config = shape([
            'looker' => shape([
                'plugins' => dict(string(), mixed()),
            ], true),
        ], true)->assert(
            $container->has('config')
                ? $container->get('config')
                : [],
        );

        /** @psalm-var ServiceManagerConfiguration $pluginConfig */
        $pluginConfig = $config['looker']['plugins'];

        return new PluginProxy(new MezzioPluginManager($container, $pluginConfig));
    }
}
