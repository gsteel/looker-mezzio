<?php

declare(strict_types=1);

namespace Looker\Mezzio;

use Laminas\ServiceManager\ServiceManager;
use Looker;
use Mezzio;

/** @psalm-import-type ServiceManagerConfiguration from ServiceManager */
final class ConfigProvider
{
    /** @return array<string, mixed> */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->dependencies(),
            'looker' => [
                'plugins' => $this->pluginDependencies(),
            ],
            'templates' => [
                'extension' => 'phtml',
                'layout' => null,
                'layoutCapturesTo' => 'content',
                'map' => [],
                'paths' => [],
            ],
        ];
    }

    /** @return ServiceManagerConfiguration */
    private function dependencies(): array
    {
        return [
            'factories' => [
                'MezzioPluginManagerImplementation' => Factory\PluginManagerFactory::class,
                'MezzioTemplateResolver' => Factory\ResolverFactory::class,
                TemplateRenderer::class => Factory\TemplateRendererFactory::class,
            ],
            'aliases' => [
                Looker\PluginManager::class => 'MezzioPluginManagerImplementation',
                Looker\Template\Resolver::class => 'MezzioTemplateResolver',
                Mezzio\Template\TemplateRendererInterface::class => TemplateRenderer::class,
            ],
        ];
    }

    /** @return ServiceManagerConfiguration */
    private function pluginDependencies(): array
    {
        return [
            'factories' => [
                Plugin\Url::class => Plugin\Factory\UrlFactory::class,
                Plugin\ServerUrl::class => Plugin\Factory\ServerUrlFactory::class,
            ],
            'aliases' => [
                'serverUrl' => Plugin\ServerUrl::class,
                'url' => Plugin\Url::class,
            ],
        ];
    }
}
