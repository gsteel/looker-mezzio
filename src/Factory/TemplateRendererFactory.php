<?php

declare(strict_types=1);

namespace Looker\Mezzio\Factory;

use GSteel\Dot;
use Looker\Mezzio\TemplateRenderer;
use Looker\PluginManager;
use Looker\Renderer\Renderer;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

final class TemplateRendererFactory
{
    public function __invoke(ContainerInterface $container): TemplateRenderer
    {
        $config = $container->has('config')
            ? $container->get('config')
            : [];
        Assert::isArray($config);

        $defaultLayout = Dot::stringOrNull('templates.layout', $config);
        $captureTo     = Dot::stringDefault('templates.layoutCapturesTo', $config, 'content');
        Assert::nullOrStringNotEmpty($defaultLayout);
        Assert::stringNotEmpty($captureTo);

        return new TemplateRenderer(
            $container->get(Renderer::class),
            $container->get(PluginManager::class),
            $defaultLayout,
            $captureTo,
        );
    }
}
