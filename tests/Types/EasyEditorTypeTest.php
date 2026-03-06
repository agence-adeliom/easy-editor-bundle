<?php

declare(strict_types=1);

namespace Adeliom\EasyEditorBundle\Tests\Types;

use Adeliom\EasyEditorBundle\Types\EasyEditorType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Adeliom\EasyEditorBundle\Types\EasyEditorType::class)]
final class EasyEditorTypeTest extends TestCase
{
    public function testTypeExposesExpectedDoctrineName(): void
    {
        self::assertSame(EasyEditorType::EASYEDITORTYPE, (new EasyEditorType())->getName());
    }
}
