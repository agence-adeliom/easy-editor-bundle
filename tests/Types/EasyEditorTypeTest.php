<?php

declare(strict_types=1);

namespace Adeliom\EasyEditorBundle\Tests\Types;

use Adeliom\EasyEditorBundle\Types\EasyEditorType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Adeliom\EasyEditorBundle\Types\EasyEditorType::class)]
final class EasyEditorTypeTest extends TestCase
{
    public function testTypeExposesExpectedDoctrineNameAndJsonConversions(): void
    {
        $type = new EasyEditorType();
        $platform = $this->createMock(AbstractPlatform::class);

        self::assertSame(EasyEditorType::EASYEDITORTYPE, $type->getName());
        self::assertSame('{"blocks":[{"type":"text","data":{"content":"Hello"}}]}', $type->convertToDatabaseValue([
            'blocks' => [
                ['type' => 'text', 'data' => ['content' => 'Hello']],
            ],
        ], $platform));
        self::assertSame([
            'blocks' => [
                ['type' => 'text', 'data' => ['content' => 'Hello']],
            ],
        ], $type->convertToPHPValue('{"blocks":[{"type":"text","data":{"content":"Hello"}}]}', $platform));
        self::assertNull($type->convertToPHPValue('', $platform));
    }
}
