<?php

declare(strict_types=1);

namespace Adeliom\EasyEditorBundle\Tests\DataCollector;

use Adeliom\EasyEditorBundle\Block\Helper;
use Adeliom\EasyEditorBundle\DataCollector\EditorCollector;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(\Adeliom\EasyEditorBundle\DataCollector\EditorCollector::class)]
final class EditorCollectorTest extends TestCase
{
    public function testCollectorStoresBlocksAndExposesDefaultName(): void
    {
        $helper = $this->createMock(Helper::class);
        $helper->method('getTraces')->willReturn(['block-1' => ['name' => 'Hero']]);

        $collector = new EditorCollector($helper);
        $collector->collect(new Request(), new Response());

        self::assertSame(['block-1' => ['name' => 'Hero']], $collector->getBlocks());
        self::assertSame(EditorCollector::class, $collector->getName());
    }

    public function testCollectorReturnsEmptyArrayWhenNoBlockWasCollected(): void
    {
        $helper = $this->createMock(Helper::class);
        $helper->method('getTraces')->willReturn([]);

        $collector = new EditorCollector($helper);
        $collector->collect(new Request(), new Response());

        self::assertSame([], $collector->getBlocks());
    }
}
