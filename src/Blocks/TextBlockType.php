<?php
namespace Adeliom\EasyEditorBundle\Blocks;

use Adeliom\EasyEditorBundle\Block\AbstractBlock;
use Adeliom\EasyEditorBundle\Block\BlockInterface;
use Adeliom\EasyMediaBundle\Form\EasyMediaType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
