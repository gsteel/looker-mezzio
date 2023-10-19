<?php

declare(strict_types=1);

namespace Looker\Mezzio\Exception;

use RuntimeException;

use function sprintf;

final class UnsupportedFeature extends RuntimeException
{
    public static function templatePathsCannotBeModifiedAtRuntime(string $addedPath): self
    {
        return new self(sprintf(
            'Template paths cannot be modified at runtime. An attempt was made to add the path "%s"',
            $addedPath,
        ));
    }

    public static function retrievalOfConfiguredPaths(): self
    {
        return new self(
            'It is not possible to retrieve the configured template paths',
        );
    }

    public static function defaultParametersAreNotSupported(): self
    {
        return new self(
            'Default template parameters are not supported because they might change per-request, and are '
            . 'therefore mutable state.',
        );
    }
}
