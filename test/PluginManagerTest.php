<?php

declare(strict_types=1);

namespace Looker\Mezzio\Test;

use Generator;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\ServiceManager;
use Looker\ConfigProvider as LookerConfigProvider;
use Looker\Mezzio\ConfigProvider as LookerMezzioProvider;
use Looker\Mezzio\PluginManager;
use Looker\PluginManager as PluginManagerInterface;
use Looker\Renderer\PluginProxy;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function class_exists;
use function Psl\Type\dict;
use function Psl\Type\mixed_dict;
use function Psl\Type\non_empty_string;
use function Psl\Type\shape;

/** @psalm-import-type ServiceManagerConfiguration from ServiceManager */
final class PluginManagerTest extends TestCase
{
    private PluginManager $pluginManager;
    private ServiceManager $container;

    #[Override]
    protected function setUp(): void
    {
        $aggregator = new ConfigAggregator([
            LookerConfigProvider::class,
            LookerMezzioProvider::class,
        ]);
        $config     = $aggregator->getMergedConfig();

        self::assertIsArray($config['dependencies']);
        $config['dependencies']['services'] = ['config' => $config];

        /** @psalm-var ServiceManagerConfiguration $dependencies */
        $dependencies    = $config['dependencies'];
        $this->container = new ServiceManager($dependencies);
        $config          = shape([
            'looker' => shape([
                'plugins' => mixed_dict(),
            ], true),
        ], true)->assert($config);

        /** @psalm-var ServiceManagerConfiguration $pluginConfig */
        $pluginConfig = $config['looker']['plugins'];

        $this->pluginManager = new PluginManager($this->container, $pluginConfig);
    }

    /** @return Generator<string, array{0: string, 1: class-string}> */
    public static function standardAliases(): Generator
    {
        $config = shape([
            'looker' => shape([
                'plugins' => shape([
                    'aliases' => dict(non_empty_string(), non_empty_string()),
                ], true),
            ], true),
        ], true)->assert((new LookerConfigProvider())->__invoke());

        foreach ($config['looker']['plugins']['aliases'] as $alias => $expectedClass) {
            self::assertTrue(class_exists($expectedClass));

            yield $alias => [$alias, $expectedClass];
        }
    }

    /** @param class-string $expectedType */
    #[DataProvider('standardAliases')]
    public function testThatStandardPluginsCanBeRetrievedByAlias(string $alias, string $expectedType): void
    {
        self::assertInstanceOf($expectedType, $this->pluginManager->get($alias));
    }

    public function testThatTheUrlPluginExists(): void
    {
        self::assertTrue($this->pluginManager->has('url'));
    }

    public function testThatTheServerUrlPluginExists(): void
    {
        self::assertTrue($this->pluginManager->has('serverUrl'));
    }

    public function testThatThePluginManagerConfiguredInTheContainerIsAProxy(): void
    {
        $manager = $this->container->get(PluginManagerInterface::class);
        self::assertInstanceOf(PluginProxy::class, $manager);
    }

    /** @param class-string $expectedType */
    #[DataProvider('standardAliases')]
    public function testThatTheProxyPluginManagerCanRetrieveTheStandardPlugins(
        string $alias,
        string $expectedType,
    ): void {
        $manager = $this->container->get(PluginManagerInterface::class);
        self::assertInstanceOf($expectedType, $manager->get($alias));
    }

    public function testPluginManagerValidatesPlugins(): void
    {
        $manager = new PluginManager(new ServiceManager(), [
            'factories' => [
                'foo' => static fn (): string => 'bar',
            ],
        ]);

        $this->expectException(InvalidServiceException::class);
        $this->expectExceptionMessage('The given service is not callable. Received string');

        $manager->get('foo');
    }
}
