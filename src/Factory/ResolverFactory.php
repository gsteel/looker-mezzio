<?php

declare(strict_types=1);

namespace Looker\Mezzio\Factory;

use GSteel\Dot;
use Looker\Template\AggregateResolver;
use Looker\Template\DirectoryResolver;
use Looker\Template\MapResolver;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

use function array_filter;

final class ResolverFactory
{
    public function __invoke(ContainerInterface $container): AggregateResolver
    {
        return new AggregateResolver(...array_filter([
            $this->mapResolver($container),
            $this->directoryResolver($container),
        ]));
    }

    private function mapResolver(ContainerInterface $container): MapResolver|null
    {
        $config = $this->config($container);
        $map    = Dot::arrayOrNull('templates.map', $config) ?? [];
        if ($map === []) {
            return null;
        }

        Assert::isMap($map);
        Assert::allStringNotEmpty($map);
        /** @psalm-var array<non-empty-string, non-empty-string> $map */

        return new MapResolver($map);
    }

    private function directoryResolver(ContainerInterface $container): DirectoryResolver|null
    {
        $config = $this->config($container);
        $list   = Dot::arrayDefault('templates.paths', $config, []);
        if ($list === []) {
            return null;
        }

        Assert::isList($list);
        Assert::allStringNotEmpty($list);
        $defaultSuffix = Dot::nonEmptyString('templates.extension', $config);

        return new DirectoryResolver($list, $defaultSuffix);
    }

    /** @return array<array-key, mixed> */
    private function config(ContainerInterface $container): array
    {
        $config = $container->has('config') ? $container->get('config') : [];
        Assert::isArray($config);

        return $config;
    }
}
