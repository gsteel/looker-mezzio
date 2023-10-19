<?php

declare(strict_types=1);

namespace Looker\Mezzio;

use Looker\Mezzio\Exception\UnsupportedFeature;
use Looker\Model\Model;
use Looker\Model\ViewModel;
use Looker\Plugin\Layout;
use Looker\PluginManager;
use Looker\Renderer\Renderer;
use Mezzio\Template\TemplateRendererInterface;

use function is_string;

final readonly class TemplateRenderer implements TemplateRendererInterface
{
    /**
     * @param non-empty-string|null $defaultLayout
     * @param non-empty-string      $captureTo
     */
    public function __construct(
        private Renderer $renderer,
        private PluginManager $plugins,
        private string|null $defaultLayout = null,
        private string $captureTo = 'content',
    ) {
    }

    /**
     * phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     *
     * @param non-empty-string                         $name
     * @param array<non-empty-string, mixed>|ViewModel $params
     */
    public function render(string $name, $params = []): string
    {
        $viewModel = $params instanceof ViewModel
            ? $params
            : Model::new($name, $params);

        $viewModel = $viewModel->withTemplate($name);

        $content = $this->renderer->render($viewModel);
        $layout  = $this->resolveLayout($viewModel);
        $buffer  = $layout === false
            ? $content
            : $this->renderer->render(
                Model::terminal($layout, [$this->captureTo => $content]),
            );

        $this->plugins->clearPluginState();

        return $buffer;
    }

    /** @return non-empty-string|false */
    private function resolveLayout(ViewModel $model): string|false
    {
        // Layout template set in the layout plugin takes precedence
        $plugin = $this->plugins->has(Layout::class)
            ? $this->plugins->get(Layout::class)
            : null;

        $layout = $plugin instanceof Layout
            ? $plugin->currentLayout()
            : null;

        if ($layout !== null) {
            return $layout;
        }

        // A custom layout in the view model has next precedence
        /** @psalm-suppress MixedAssignment */
        $custom = $model->variables()['layout'] ?? null;
        if (is_string($custom) && $custom !== '') {
            return $custom;
        }

        if ($custom === false || $this->defaultLayout === null) {
            return false;
        }

        return $this->defaultLayout;
    }

    /** @throws UnsupportedFeature */
    public function addPath(string $path, string|null $namespace = null): never
    {
        throw UnsupportedFeature::templatePathsCannotBeModifiedAtRuntime($path);
    }

    /** @throws UnsupportedFeature */
    public function getPaths(): never
    {
        throw UnsupportedFeature::retrievalOfConfiguredPaths();
    }

    /** @throws UnsupportedFeature */
    public function addDefaultParam(string $templateName, string $param, mixed $value): never
    {
        throw UnsupportedFeature::defaultParametersAreNotSupported();
    }
}
