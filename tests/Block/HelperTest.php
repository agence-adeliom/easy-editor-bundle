<?php

declare(strict_types=1);

namespace Adeliom\EasyEditorBundle\Tests\Block;

use Adeliom\EasyEditorBundle\Block\AbstractBlock;
use Adeliom\EasyEditorBundle\Block\BlockCollection;
use Adeliom\EasyEditorBundle\Block\Helper;
use Adeliom\EasyEditorBundle\Event\RenderBlockEvent;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Markup;

#[CoversClass(\Adeliom\EasyEditorBundle\Block\Helper::class)]
final class HelperTest extends TestCase
{
    public function testIncludeAssetsRendersCssJavascriptAndWebpackEntries(): void
    {
        $twig = new Environment(new ArrayLoader());
        $helper = new Helper(
            $twig,
            $this->createMock(EventDispatcherInterface::class),
            new BlockCollection([]),
            $this->createMock(FormFactory::class),
            $this->createMock(EntityManagerInterface::class)
        );

        $assetsProperty = new \ReflectionProperty($helper, 'assets');
        $assetsProperty->setValue($helper, [
            'js' => ['/app.js'],
            'css' => ['/app.css'],
            'webpack' => ['editor'],
        ]);

        $html = $helper->includeAssets();

        self::assertStringContainsString("@import url(/app.css);", $html);
        self::assertStringContainsString('<script src="/app.js" type="text/javascript"></script>', $html);
    }

    public function testRenderEasyEditorBlockTracksRenderingAndReturnsMarkup(): void
    {
        $block = new class($this->createMock(EntityManagerInterface::class)) extends AbstractBlock {
            public function getName(): string
            {
                return 'Hero';
            }

            public function getIcon(): string|array
            {
                return 'fa-hero';
            }

            public function getTemplate(): string
            {
                return '@EasyEditor/hero.html.twig';
            }

            public function getPosition(): int
            {
                return 10;
            }

            public static function configureAssets(): array
            {
                return [
                    'js' => ['/hero.js'],
                    'css' => ['/hero.css'],
                    'webpack' => [],
                ];
            }

            public function buildBlock(FormBuilderInterface $builder, array $options): void
            {
                $builder->add('content', TextType::class);
            }
        };

        $collection = new BlockCollection([$block]);

        $twig = $this->createMock(Environment::class);
        $twig->expects(self::once())
            ->method('render')
            ->with('@EasyEditor/hero.html.twig', self::callback(static function (array $parameters): bool {
                return 'landing' === $parameters['page']
                    && $parameters['blockType'] !== ''
                    && 'custom-id' === $parameters['settings']['attr_id']
                    && !isset($parameters['settings']['position'])
                    && 'extra' === $parameters['extra'];
            }))
            ->willReturn('<section>Hero</section>');

        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(RenderBlockEvent::class, static function (RenderBlockEvent $event): void {
            $event->setAssets([
                'js' => ['/hero.js'],
                'css' => ['/hero.css'],
                'webpack' => [],
            ]);
        });

        $helper = new Helper(
            $twig,
            $dispatcher,
            $collection,
            $this->createMock(FormFactory::class),
            $this->createMock(EntityManagerInterface::class)
        );

        $markup = $helper->renderEasyEditorBlock(
            $twig,
            ['page' => 'landing'],
            [
                'block_type' => $block::class,
                'position' => 7,
                'attr_id' => 'custom-id',
            ],
            ['extra' => 'extra']
        );

        self::assertInstanceOf(Markup::class, $markup);
        self::assertSame('<section>Hero</section>', (string) $markup);
        self::assertCount(1, $helper->getTraces());

        $trace = array_values($helper->getTraces())[0];
        self::assertSame('Hero', $trace['name']);
        self::assertSame(7, $trace['position']);
        self::assertSame('custom-id', $trace['settings']['attr_id']);

        $assetsHtml = $helper->includeAssets();
        self::assertStringContainsString('/hero.css', $assetsHtml);
        self::assertStringContainsString('/hero.js', $assetsHtml);
    }

    public function testRenderEasyEditorBlockKeepsLegacyGenericListenersWorking(): void
    {
        $block = new class($this->createMock(EntityManagerInterface::class)) extends AbstractBlock {
            public function getName(): string
            {
                return 'Legacy Hero';
            }

            public function getIcon(): string|array
            {
                return 'fa-hero';
            }

            public function getTemplate(): string
            {
                return '@EasyEditor/legacy.html.twig';
            }

            public function getPosition(): int
            {
                return 10;
            }

            public static function configureAssets(): array
            {
                return ['js' => [], 'css' => [], 'webpack' => []];
            }

            public function buildBlock(FormBuilderInterface $builder, array $options): void
            {
                $builder->add('content', TextType::class);
            }
        };

        $twig = $this->createMock(Environment::class);
        $twig->expects(self::once())
            ->method('render')
            ->with('@EasyEditor/legacy.html.twig', self::callback(static function (array $parameters): bool {
                return 'legacy-id' === $parameters['settings']['attr_id'];
            }))
            ->willReturn('<section>Legacy</section>');

        $dispatcher = new EventDispatcher();
        $dispatcher->addListener('easy_editor.render_block', static function (GenericEvent $event): void {
            $settings = $event->getArgument('settings');
            $settings['attr_id'] = 'legacy-id';
            $event->setArgument('settings', $settings);
        });

        $helper = new Helper(
            $twig,
            $dispatcher,
            new BlockCollection([$block]),
            $this->createMock(FormFactory::class),
            $this->createMock(EntityManagerInterface::class)
        );

        $markup = $helper->renderEasyEditorBlock(
            $twig,
            ['page' => 'landing'],
            ['block_type' => $block::class],
            []
        );

        self::assertInstanceOf(Markup::class, $markup);
        self::assertSame('<section>Legacy</section>', (string) $markup);
    }
}
