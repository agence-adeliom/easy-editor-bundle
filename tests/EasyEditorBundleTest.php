<?php

declare(strict_types=1);

namespace Adeliom\EasyEditorBundle\Tests;

use Adeliom\EasyEditorBundle\DependencyInjection\EasyEditorExtension;
use Adeliom\EasyEditorBundle\EasyEditorBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Adeliom\EasyEditorBundle\EasyEditorBundle::class)]
final class EasyEditorBundleTest extends TestCase
{
    public function testBundleCreatesItsContainerExtension(): void
    {
        self::assertInstanceOf(EasyEditorExtension::class, (new EasyEditorBundle())->getContainerExtension());
    }
}
