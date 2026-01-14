<?php

declare(strict_types=1);

namespace Looker\Mezzio\Factory;

use Looker\Mezzio\TemplateRenderer;
use Looker\PluginManager;
use Looker\Renderer\Renderer;
use Psr\Container\ContainerInterface;

use function Psl\Type\non_empty_string;
use function Psl\Type\null;
use function Psl\Type\optional;
use function Psl\Type\shape;
use function Psl\Type\union;

final class TemplateRendererFactory
{
    public function __invoke(ContainerInterface $container): TemplateRenderer
    {
        $config = shape([
            'templates' => optional(shape([
                'layout' => optional(union(non_empty_string(), null())),
                'layoutCapturesTo' => optional(union(non_empty_string(), null())),
            ], true)),
        ], true)->assert(
            $container->has('config')
                ? $container->get('config')
                : [],
        );

        $defaultLayout = $config['templates']['layout'] ?? null;
        $captureTo     = $config['templates']['layoutCapturesTo'] ?? 'content';

        return new TemplateRenderer(
            $container->get(Renderer::class),
            $container->get(PluginManager::class),
            $defaultLayout,
            $captureTo,
        );
    }
}
