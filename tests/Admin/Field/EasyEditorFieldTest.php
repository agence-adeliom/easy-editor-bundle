<?php

declare(strict_types=1);

namespace Adeliom\EasyEditorBundle\Tests\Admin\Field;

use Adeliom\EasyEditorBundle\Admin\Field\EasyEditorField;
use Adeliom\EasyEditorBundle\Form\EditorCollectionType;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetDto;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Adeliom\EasyEditorBundle\Admin\Field\EasyEditorField::class)]
final class EasyEditorFieldTest extends TestCase
{
    public function testFieldConfiguresExpectedDefaultsAndMutators(): void
    {
        $dto = EasyEditorField::new('content', 'Content')
            ->allowDrag(false)
            ->allowAdd(false)
            ->allowDelete(false)
            ->setEntryIsComplex(true)
            ->showEntryLabel()
            ->renderExpanded()
            ->allowedBlocks(['hero', 'gallery'])
            ->getAsDto();

        self::assertSame('content', $dto->getProperty());
        self::assertSame('Content', $dto->getLabel());
        self::assertSame(EditorCollectionType::class, $dto->getFormType());
        self::assertSame('crud/field/collection', $dto->getTemplateName());
        self::assertStringContainsString('field-collection_sortable', $dto->getCssClass());
        self::assertSame('col-12', $dto->getDefaultColumns());
        self::assertFalse($dto->getCustomOption(EasyEditorField::OPTION_ALLOW_DRAG));
        self::assertFalse($dto->getCustomOption(EasyEditorField::OPTION_ALLOW_ADD));
        self::assertFalse($dto->getCustomOption(EasyEditorField::OPTION_ALLOW_DELETE));
        self::assertTrue($dto->getCustomOption(EasyEditorField::OPTION_ENTRY_IS_COMPLEX));
        self::assertTrue($dto->getCustomOption(EasyEditorField::OPTION_SHOW_ENTRY_LABEL));
        self::assertTrue($dto->getCustomOption(EasyEditorField::OPTION_RENDER_EXPANDED));
        self::assertSame(['hero', 'gallery'], $dto->getCustomOption(EasyEditorField::OPTION_BLOCKS));
        self::assertCount(1, $dto->getAssets()->getJsAssets());
        self::assertCount(1, $dto->getAssets()->getCssAssets());
        self::assertContainsOnlyInstancesOf(AssetDto::class, $dto->getAssets()->getCssAssets());
    }
}
