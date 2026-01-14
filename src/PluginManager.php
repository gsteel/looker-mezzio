<?php

declare(strict_types=1);

namespace Looker\Mezzio;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Override;

use function get_debug_type;
use function is_callable;
use function sprintf;

/** @extends AbstractPluginManager<callable> */
final class PluginManager extends AbstractPluginManager
{
    #[Override]
    public function validate(mixed $instance): void
    {
        if (is_callable($instance)) {
            return;
        }

        throw new InvalidServiceException(
            sprintf(
                'The given service is not callable. Received %s',
                get_debug_type($instance),
            ),
        );
    }
}
