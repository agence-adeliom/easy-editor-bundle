<?php

namespace Adeliom\EasyEditorBundle\Tests\DependencyInjection;

use Adeliom\EasyEditorBundle\Block\BlockInterface;
use Adeliom\EasyEditorBundle\DependencyInjection\EasyEditorExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[CoversClass(\Adeliom\EasyEditorBundle\DependencyInjection\EasyEditorExtension::class)]
final class EasyEditorExtensionTest extends TestCase
{
    public function testExtensionLoadsServicesAndAutoconfiguration(): void
    {
        $container = new ContainerBuilder();
        $extension = new EasyEditorExtension();

        $extension->load([], $container);

        self::assertSame('easy_editor', $extension->getAlias());
        self::assertTrue($container->hasDefinition('Adeliom\\EasyEditorBundle\\Form\\EditorCollectionType'));
        self::assertTrue($container->hasDefinition('Adeliom\\EasyEditorBundle\\Block\\BlockCollection'));
        self::assertTrue($container->hasDefinition('Adeliom\\EasyEditorBundle\\Block\\Helper'));
        self::assertTrue($container->hasDefinition('Adeliom\\EasyEditorBundle\\Twig\\EasyBlockExtension'));
        self::assertTrue($container->hasAlias('easy_editor.block_collection'));
        self::assertTrue($container->hasAlias('easy_editor.block_helper'));
        self::assertTrue($container->hasAlias('easy_editor.twig_extension'));

        $autoconfiguration = $container->getAutoconfiguredInstanceof()[BlockInterface::class] ?? null;

        self::assertInstanceOf(ChildDefinition::class, $autoconfiguration);
        self::assertSame(['easy_editor.block' => [[]]], $autoconfiguration->getTags());
    }
}
