<?php
namespace Adeliom\EasyEditorBundle\Blocks;

use Adeliom\EasyEditorBundle\Block\AbstractBlock;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TextBlockType extends AbstractBlock
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add("content", CKEditorType::class, [
                "required" => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
        ;
    }


    public function getName(): string
    {
        return 'Text';
    }

    public function getIcon(): string
    {
        return '<span class="fas fa-file"></span>';
    }

    public function getTemplate(): string
    {
        return "@EasyEditor/block/text.html.twig";
    }
}
