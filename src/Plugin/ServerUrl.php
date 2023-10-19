<?php

declare(strict_types=1);

namespace Looker\Mezzio\Plugin;

use Mezzio\Helper\ServerUrlHelper;

final readonly class ServerUrl
{
    public function __construct(
        private ServerUrlHelper $urlHelper,
    ) {
    }

    /** @param non-empty-string|null $path */
    public function __invoke(string|null $path = null): string
    {
        return $this->urlHelper->generate($path);
    }
}
