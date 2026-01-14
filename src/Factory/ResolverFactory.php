<?php

declare(strict_types=1);

namespace Looker\Mezzio\Factory;

use Looker\Template\AggregateResolver;
use Looker\Template\DirectoryResolver;
use Looker\Template\MapResolver;
use Psr\Container\ContainerInterface;

use function Psl\Type\array_key;
use function Psl\Type\dict;
use function Psl\Type\mixed;
use function Psl\Type\non_empty_string;
use function Psl\Type\optional;
use function Psl\Type\shape;
use function Psl\Type\vec;
use function Psl\Vec\filter_nulls;

final class ResolverFactory
{
    public function __invoke(ContainerInterface $container): AggregateResolver
    {
        return new AggregateResolver(...filter_nulls([
            $this->mapResolver($container),
            $this->directoryResolver($container),
        ]));
    }

    private function mapResolver(ContainerInterface $container): MapResolver|null
    {
        $config = optional(shape([
            'templates' => optional(shape([
                'map' => optional(dict(non_empty_string(), non_empty_string())),
            ], true)),
        ], true))->assert($this->config($container));

        $map = $config['templates']['map'] ?? [];
        if ($map === []) {
            return null;
        }

        return new MapResolver($map);
    }

    private function directoryResolver(ContainerInterface $container): DirectoryResolver|null
    {
        $config = shape([
            'templates' => shape([
                'paths' => optional(vec(non_empty_string())),
                'extension' => optional(non_empty_string()),
            ], true),
        ], true)->assert($this->config($container));
        $list   = $config['templates']['paths'] ?? [];

        if ($list === []) {
            return null;
        }

        return new DirectoryResolver(
            $list,
            non_empty_string()->assert($config['templates']['extension'] ?? null),
        );
    }

    /** @return array<array-key, mixed> */
    private function config(ContainerInterface $container): array
    {
        return dict(array_key(), mixed())->assert(
            $container->has('config') ? $container->get('config') : [],
        );
    }
}
