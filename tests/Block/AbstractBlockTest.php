<?php

namespace Adeliom\EasyEditorBundle\Tests\Block;

use Adeliom\EasyEditorBundle\Block\AbstractBlock;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[CoversClass(\Adeliom\EasyEditorBundle\Block\AbstractBlock::class)]
final class AbstractBlockTest extends TestCase
{
    public function testBlockExposesManagerAndDefaultMetadata(): void
    {
        $manager = $this->createMock(EntityManagerInterface::class);
        $block = new class ($manager) extends AbstractBlock {
            public function getName(): string
            {
                return 'Hero';
            }

            public function getIcon(): string|array
            {
                return ['fa-hero', 'fa-alt'];
            }

            public function getTemplate(): string
            {
                return '@EasyEditor/hero.html.twig';
            }

            public function buildBlock(FormBuilderInterface $builder, array $options): void
            {
                $builder->add('content', TextType::class);
            }
        };

        self::assertSame($manager, $block->getManager());
        self::assertSame(['js' => [], 'css' => [], 'webpack' => []], $block::configureAssets());
        self::assertSame(['js' => [], 'css' => []], $block::configureAdminAssets());
        self::assertSame([], $block::configureAdminFormTheme());
        self::assertSame([], $block::indexableProperties());
        self::assertSame(100, $block->getPosition());
        self::assertTrue($block->supports(\stdClass::class));
    }

    public function testBuildFormBuildViewAndOptionsUseExpectedDefaults(): void
    {
        $block = new class ($this->createMock(EntityManagerInterface::class)) extends AbstractBlock {
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

            public function buildBlock(FormBuilderInterface $builder, array $options): void
            {
                $builder->add('content', TextType::class);
            }
        };

        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(3))
            ->method('add')
            ->willReturnCallback(function (string $name, string $type, array $options = []) use ($builder, $block): FormBuilderInterface {
                static $calls = 0;
                ++$calls;

                if (1 === $calls) {
                    TestCase::assertSame('block_type', $name);
                    TestCase::assertSame(HiddenType::class, $type);
                    TestCase::assertSame($block::class, $options['data']);
                }

                if (2 === $calls) {
                    TestCase::assertSame('position', $name);
                    TestCase::assertSame(HiddenType::class, $type);
                }

                if (3 === $calls) {
                    TestCase::assertSame('content', $name);
                    TestCase::assertSame(TextType::class, $type);
                }

                return $builder;
            });

        $block->buildForm($builder, []);

        $view = new FormView();
        $view->vars['attr'] = [];
        $block->buildView($view, $this->createMock(FormInterface::class), []);

        self::assertSame('Hero', $view->vars['attr']['block-title']);
        self::assertSame('fa-hero', $view->vars['attr']['block-icon']);

        $resolver = new OptionsResolver();
        $block->configureOptions($resolver);

        self::assertTrue($resolver->resolve()['cascade_validation']);
    }
}
