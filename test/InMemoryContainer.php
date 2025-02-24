<?php

declare(strict_types=1);

namespace Looker\Mezzio\Test;

use Override;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

use function array_key_exists;
use function sprintf;

/** @internal */
final class InMemoryContainer implements ContainerInterface
{
    /** @param array<string, mixed> $services */
    public function __construct(public array $services = [])
    {
    }

    /**
     * @param string|class-string<T> $id
     *
     * @return ($id is class-string ? T : mixed)
     *
     * @template T
     * @psalm-suppress MixedReturnStatement
     */
    #[Override]
    public function get($id): mixed
    {
        if (! $this->has($id)) {
            throw new class (
                sprintf('Service not found: "%s"', $id),
            ) extends RuntimeException implements NotFoundExceptionInterface {
            };
        }

        return $this->services[$id];
    }

    #[Override]
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }

    public function setService(string $id, mixed $service): void
    {
        $this->services[$id] = $service;
    }
}
