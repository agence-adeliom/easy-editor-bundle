<?php

namespace Adeliom\EasyEditorBundle\Tests\Fixtures\Block;

use Adeliom\EasyEditorBundle\Block\AbstractBlock;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class TestBlockType extends AbstractBlock
{
    public function getName(): string
    {
        return 'Test block';
    }

    public function getIcon(): string|array
    {
        return ['fa-test'];
    }

    public function getTemplate(): string
    {
        return '@EasyEditor/test_block.html.twig';
    }

    public function getBlockPrefix(): string
    {
        return 'test_block';
    }

    public function getPosition(): int
    {
        return 50;
    }

    public function buildBlock(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('content', TextType::class);
    }
}
