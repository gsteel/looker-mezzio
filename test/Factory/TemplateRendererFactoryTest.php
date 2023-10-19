<?php

declare(strict_types=1);

namespace Looker\Mezzio\Test\Factory;

use Looker\Mezzio\Factory\TemplateRendererFactory;
use Looker\Mezzio\TemplateRenderer;
use Looker\Mezzio\Test\InMemoryContainer;
use Looker\PluginManager;
use Looker\Renderer\Renderer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TemplateRendererFactoryTest extends TestCase
{
    /** @return array<string, array{0: array<string, mixed>}> */
    public static function configProvider(): array
    {
        return [
            'No Config' => [[]],
            'With Default Layout Specified' => [
                [
                    'config' => [
                        'templates' => ['layout' => 'layout::default'],
                    ],
                ],
            ],
            'With Capture To Specified' => [
                [
                    'config' => [
                        'templates' => ['layoutCapturesTo' => 'hitMe'],
                    ],
                ],
            ],
            'With All Options' => [
                [
                    'config' => [
                        'templates' => [
                            'layout' => 'layout::default',
                            'layoutCapturesTo' => 'hitMe',
                        ],
                    ],
                ],
            ],
        ];
    }

    /** @param array<string, mixed> $config */
    #[DataProvider('configProvider')]
    public function testValidConfig(array $config): void
    {
        $container = new InMemoryContainer($config);
        $container->setService(Renderer::class, $this->createMock(Renderer::class));
        $container->setService(PluginManager::class, $this->createMock(PluginManager::class));

        self::assertInstanceOf(TemplateRenderer::class, (new TemplateRendererFactory())($container));
    }
}
