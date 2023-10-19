<?php

declare(strict_types=1);

namespace Looker\Mezzio\Test\Factory;

use Looker\Mezzio\Factory\ResolverFactory;
use Looker\Mezzio\Test\InMemoryContainer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Throwable;

class ResolverFactoryTest extends TestCase
{
    /** @return array<string, array{0: array<string, mixed>}> */
    public static function invalidConfigProvider(): array
    {
        return [
            'Map resolver: Map is List' => [
                [
                    'config' => [
                        'templates' => [
                            'map' => [0 => 'bar'],
                        ],
                    ],
                ],
            ],
            'Map resolver: map with empty values' => [
                [
                    'config' => [
                        'templates' => [
                            'map' => ['empty' => ''],
                        ],
                    ],
                ],
            ],
            'Directory resolver: Not list' => [
                [
                    'config' => [
                        'templates' => [
                            'paths' => ['foo' => 'bar'],
                        ],
                    ],
                ],
            ],
            'Directory resolver: List with empty values' => [
                [
                    'config' => [
                        'templates' => [
                            'paths' => [0 => ''],
                        ],
                    ],
                ],
            ],
            'Directory resolver: Empty extension' => [
                [
                    'config' => [
                        'templates' => [
                            'paths' => ['some-dir'],
                            'extension' => '',
                        ],
                    ],
                ],
            ],
            'Directory resolver: Null extension' => [
                [
                    'config' => [
                        'templates' => [
                            'paths' => ['some-dir'],
                            'extension' => null,
                        ],
                    ],
                ],
            ],
            'Directory resolver: Extension not string' => [
                [
                    'config' => [
                        'templates' => [
                            'paths' => ['some-dir'],
                            'extension' => [],
                        ],
                    ],
                ],
            ],
        ];
    }

    /** @param array<string, mixed> $config */
    #[DataProvider('invalidConfigProvider')]
    public function testInvalidConfig(array $config): void
    {
        $this->expectException(Throwable::class);
        (new ResolverFactory())(new InMemoryContainer($config));
    }

    /** @return array<string, array{0: array<string, mixed>}> */
    public static function configProvider(): array
    {
        return [
            'Map resolver' => [
                [
                    'config' => [
                        'templates' => [
                            'map' => ['empty' => __DIR__ . '/templates/empty.phtml'],
                        ],
                    ],
                ],
            ],
            'Directory resolver' => [
                [
                    'config' => [
                        'templates' => [
                            'paths' => [__DIR__ . '/templates'],
                            'extension' => 'phtml',
                        ],
                    ],
                ],
            ],
        ];
    }

    /** @param array<string, mixed> $config */
    #[DataProvider('configProvider')]
    public function testTemplatesResolve(array $config): void
    {
        $resolver = (new ResolverFactory())(new InMemoryContainer($config));

        self::assertSame(__DIR__ . '/templates/empty.phtml', $resolver->resolve('empty'));
    }
}
