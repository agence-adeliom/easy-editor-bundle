<?php

namespace Adeliom\EasyEditorBundle\Tests\Form;

use Adeliom\EasyEditorBundle\Block\BlockCollection;
use Adeliom\EasyEditorBundle\Form\EditorCollectionType;
use Adeliom\EasyEditorBundle\Tests\Fixtures\Block\TestBlockType;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

#[CoversClass(\Adeliom\EasyEditorBundle\Form\EditorCollectionType::class)]
final class EditorCollectionTypeTest extends TypeTestCase
{
    private TestBlockType $block;

    protected function getExtensions(): array
    {
        $this->block = new TestBlockType($this->createMock(EntityManagerInterface::class));

        return [
            new PreloadedExtension([
                new EditorCollectionType(new BlockCollection([$this->block])),
                $this->block,
            ], []),
        ];
    }

    public function testTypeBuildsViewWithBlocksAndPrototypes(): void
    {
        $form = $this->factory->create(EditorCollectionType::class, [
            [
                'block_type' => TestBlockType::class,
                'position' => 20,
                'content' => 'First',
            ],
        ], [
            'allow_add' => true,
            'allow_delete' => true,
            'allow_drag' => true,
        ]);

        $view = $form->createView();

        self::assertSame('editor_collection', $form->getConfig()->getType()->getInnerType()->getBlockPrefix());
        self::assertTrue($view->vars['allow_drag']);
        self::assertTrue($view->vars['allow_add']);
        self::assertTrue($view->vars['allow_delete']);
        self::assertArrayHasKey(TestBlockType::class, $view->vars['blocks']);
        self::assertArrayHasKey(TestBlockType::class, $view->vars['prototypes']);
        self::assertContains('editor_collection_entry', $view[0]->vars['block_prefixes']);
        self::assertContains('editor_collection_entry', $view->vars['prototypes'][TestBlockType::class]->vars['block_prefixes']);
    }

    public function testTypeNormalizesEntryOptionsAndDefaultBlocks(): void
    {
        $type = new EditorCollectionType(new BlockCollection([$this->block]));
        $resolver = new \Symfony\Component\OptionsResolver\OptionsResolver();

        $type->configureOptions($resolver);
        $options = $resolver->resolve([
            'entry_options' => ['attr' => ['class' => 'editor-entry']],
        ]);

        self::assertSame('entry', $options['entry_options']['block_name']);
        self::assertSame([TestBlockType::class], array_keys($options['blocks']));
    }
}
