<?php

declare(strict_types=1);

namespace Looker\Mezzio\Test;

use Generator;
use GSteel\Dot;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ServiceManager\ServiceManager;
use Looker\ConfigProvider as LookerConfigProvider;
use Looker\Mezzio\ConfigProvider as LookerMezzioProvider;
use Looker\Mezzio\PluginManager;
use Looker\PluginManager as PluginManagerInterface;
use Looker\Renderer\PluginProxy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\Assert;

/** @psalm-import-type ServiceManagerConfiguration from ServiceManager */
class PluginManagerTest extends TestCase
{
    private PluginManager $pluginManager;
    private ServiceManager $container;

    protected function setUp(): void
    {
        $aggregator = new ConfigAggregator([
            LookerConfigProvider::class,
            LookerMezzioProvider::class,
        ]);
        $config     = $aggregator->getMergedConfig();

        self::assertIsArray($config['dependencies']);
        $config['dependencies']['services'] = ['config' => $config];

        $this->container = new ServiceManager($config['dependencies']);
        /** @psalm-var ServiceManagerConfiguration $pluginConfig */
        $pluginConfig        = Dot::array('looker.plugins', $config);
        $this->pluginManager = new PluginManager($this->container, $pluginConfig);
    }

    /** @return Generator<string, array{0: string, 1: class-string}> */
    public static function standardAliases(): Generator
    {
        $config  = (new LookerConfigProvider())();
        $plugins = Dot::array('looker.plugins.aliases', $config);

        foreach ($plugins as $alias => $expectedClass) {
            self::assertIsString($alias);
            self::assertIsString($expectedClass);
            Assert::classExists($expectedClass);

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
}
