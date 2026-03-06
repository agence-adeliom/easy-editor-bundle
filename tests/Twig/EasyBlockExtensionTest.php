<?php

namespace Adeliom\EasyEditorBundle\Tests\Twig;

use Adeliom\EasyEditorBundle\Twig\EasyBlockExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

#[CoversClass(\Adeliom\EasyEditorBundle\Twig\EasyBlockExtension::class)]
final class EasyBlockExtensionTest extends TestCase
{
    public function testExtensionRegistersExpectedTwigFunctions(): void
    {
        $extension = new EasyBlockExtension();
        $functions = $extension->getFunctions();

        self::assertContainsOnlyInstancesOf(TwigFunction::class, $functions);
        self::assertSame(
            ['easy_editor_block', 'easy_editor_assets'],
            array_map(static fn (TwigFunction $function): string => $function->getName(), $functions)
        );
    }
}
