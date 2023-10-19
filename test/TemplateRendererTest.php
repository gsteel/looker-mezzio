<?php

declare(strict_types=1);

namespace Looker\Mezzio\Test;

use Looker\Mezzio\Exception\UnsupportedFeature;
use Looker\Mezzio\TemplateRenderer;
use Looker\Model\Model;
use Looker\Plugin\Layout;
use Looker\Renderer\PhpRenderer;
use Looker\Renderer\PluginProxy;
use Looker\Template\DirectoryResolver;
use PHPUnit\Framework\TestCase;

use function basename;

class TemplateRendererTest extends TestCase
{
    private TemplateRenderer $renderer;

    protected function setUp(): void
    {
        $container    = new InMemoryContainer();
        $layoutPlugin = new Layout();
        $container->setService('layout', $layoutPlugin);
        $container->setService(Layout::class, $layoutPlugin);
        $plugins = new PluginProxy($container);

        $this->renderer = new TemplateRenderer(
            new PhpRenderer(
                new DirectoryResolver([__DIR__ . '/templates'], 'phtml'),
                $plugins,
            ),
            $plugins,
            'layout/default',
            'content',
        );
    }

    public function testThatTheDefaultLayoutWillBeUsedWhenNoLayoutHasBeenSpecified(): void
    {
        $content = $this->renderer->render('basic-view');
        self::assertStringContainsString('<h1>Default Layout</h1>', $content);
        self::assertStringContainsString(basename(__DIR__ . '/templates/basic-view.phtml'), $content);
    }

    public function testThatACustomLayoutWillBeUsedWhenSpecifiedInParameters(): void
    {
        $content = $this->renderer->render('basic-view', ['layout' => 'layout/custom']);
        self::assertStringContainsString('<h1>Custom Layout</h1>', $content);
        self::assertStringContainsString(basename(__DIR__ . '/templates/basic-view.phtml'), $content);
    }

    public function testThatNoLayoutWillBeUsedWhenTheLayoutParamIsExplicitlySetToFalse(): void
    {
        $content = $this->renderer->render('basic-view', ['layout' => false]);
        self::assertStringNotContainsString('<h1>', $content);
        self::assertStringContainsString(basename(__DIR__ . '/templates/basic-view.phtml'), $content);
    }

    public function testThatTheLayoutSpecifiedByCallingTheLayoutPluginWillBeUsed(): void
    {
        $content = $this->renderer->render('set-layout-with-plugin');
        self::assertStringContainsString('<h1>Plugin Layout</h1>', $content);
        self::assertStringContainsString(basename(__DIR__ . '/templates/set-layout-with-plugin.phtml'), $content);
    }

    public function testThatTheLayoutSetInTheLayoutPluginWillBeUsedInPreferenceToThatSpecifiedInParams(): void
    {
        $content = $this->renderer->render('set-layout-with-plugin', ['layout' => 'layout/custom']);
        self::assertStringContainsString('<h1>Plugin Layout</h1>', $content);
        self::assertStringContainsString(basename(__DIR__ . '/templates/set-layout-with-plugin.phtml'), $content);
    }

    public function testThatTheLayoutSetInTheLayoutPluginWillBeUsedInPreferenceToExplicitFalse(): void
    {
        $content = $this->renderer->render('set-layout-with-plugin', ['layout' => false]);
        self::assertStringContainsString('<h1>Plugin Layout</h1>', $content);
        self::assertStringContainsString(basename(__DIR__ . '/templates/set-layout-with-plugin.phtml'), $content);
    }

    public function testThatPluginStateIsClearedBetweenSequentialRenders(): void
    {
        $content = $this->renderer->render('set-layout-with-plugin');
        self::assertStringContainsString('<h1>Plugin Layout</h1>', $content);

        $content = $this->renderer->render('basic-view');
        self::assertStringContainsString('<h1>Default Layout</h1>', $content);
    }

    public function testThatVariablesAreAppliedToViewsWhenUsingAnArray(): void
    {
        $content = $this->renderer->render('with-variable', ['variable' => 'Hey']);
        self::assertStringContainsString('<p>Hey</p>', $content);
    }

    public function testThatVariablesAreAppliedToViewsWhenUsingAViewModel(): void
    {
        $content = $this->renderer->render('with-variable', Model::new('whatever', ['variable' => 'Hey']));
        self::assertStringContainsString('<p>Hey</p>', $content);
    }

    public function testThatTheTemplateSetInTheModelIsIgnored(): void
    {
        $content = $this->renderer->render('basic-view', Model::new('with-variable', ['variable' => 'Hey']));
        self::assertStringNotContainsString('<p>Hey</p>', $content);
        self::assertStringContainsString(basename(__DIR__ . '/templates/basic-view.phtml'), $content);
    }

    public function testThatTemplatePathsCannotBeAdded(): void
    {
        $this->expectException(UnsupportedFeature::class);
        $this->renderer->addPath('foo');
    }

    public function testThatConfiguredPathsCannotBeRetrieved(): void
    {
        $this->expectException(UnsupportedFeature::class);
        $this->renderer->getPaths();
    }

    public function testThatDefaultParametersCannotBeSet(): void
    {
        $this->expectException(UnsupportedFeature::class);
        $this->renderer->addDefaultParam('foo', 'bar', 'baz');
    }
}
