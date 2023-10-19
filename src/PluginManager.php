<?php

declare(strict_types=1);

namespace Looker\Mezzio;

use Laminas\ServiceManager\AbstractPluginManager;
use Webmozart\Assert\Assert;

/** @extends AbstractPluginManager<callable> */
final class PluginManager extends AbstractPluginManager
{
    public function validate(mixed $instance): void
    {
        Assert::isCallable($instance);
    }
}
