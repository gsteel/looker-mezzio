<?php

declare(strict_types=1);

namespace Looker\Mezzio;

use Laminas\ServiceManager\AbstractPluginManager;
use Override;
use Webmozart\Assert\Assert;

/** @extends AbstractPluginManager<callable> */
final class PluginManager extends AbstractPluginManager
{
    #[Override]
    public function validate(mixed $instance): void
    {
        Assert::isCallable($instance);
    }
}
