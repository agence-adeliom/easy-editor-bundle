<?php
namespace Adeliom\EasyEditorBundle\Blocks;

use Adeliom\EasyEditorBundle\Block\AbstractBlock;
use Adeliom\EasyEditorBundle\Block\BlockInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EditorType extends AbstractBlock
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add("content", CKEditorType::class, [
                "required" => true,
            ]);
    }

    public function getName(): string
    {
        return 'Editor';
    }

    public function getIcon(): string
    {
        return '<span class="fas fa-paragraph"></span>';
    }

    public function getTemplate(): string
    {
        return "@EasyEditor/block/editor.html.twig";
    }
}
