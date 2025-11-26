<?php

declare(strict_types=1);

namespace Looker\Mezzio\Exception;

use RuntimeException;

final class UnsupportedFeature extends RuntimeException
{
    public static function defaultParametersAreNotSupported(): self
    {
        return new self(
            'Default template parameters are not supported because they might change per-request, and are '
            . 'therefore mutable state.',
        );
    }
}
