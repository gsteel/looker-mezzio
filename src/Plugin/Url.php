<?php

declare(strict_types=1);

namespace Looker\Mezzio\Plugin;

use Mezzio\Helper\UrlHelperInterface;

/** @psalm-import-type UrlGeneratorOptions from UrlHelperInterface */
final readonly class Url
{
    public function __construct(private UrlHelperInterface $helper)
    {
    }

    /**
     * Proxies to `Mezzio\Helper\UrlHelper::generate()`
     *
     * @param non-empty-string|null $routeName
     * @param array<string, mixed>  $routeParams
     * @param array<string, mixed>  $queryParams
     * @param array<string, mixed>  $options     Can have the following keys:
     *                                           - router (array): contains options to be passed to the router
     *                                           - reuse_result_params (bool): indicates if the current RouteResult
     *                                             parameters will be used, defaults to true
     * @psalm-param UrlGeneratorOptions $options
     */
    public function __invoke(
        string|null $routeName = null,
        array $routeParams = [],
        array $queryParams = [],
        string|null $fragmentIdentifier = null,
        array $options = [],
    ): string {
        return $this->helper->generate($routeName, $routeParams, $queryParams, $fragmentIdentifier, $options);
    }
}
