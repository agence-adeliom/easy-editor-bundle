<?php
namespace Adeliom\EasyEditorBundle\Blocks;

use Adeliom\EasyEditorBundle\Block\AbstractBlock;
use Adeliom\EasyMediaBundle\Form\EasyMediaType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class MediaTextType extends AbstractBlock
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add("type", ChoiceType::class, [
                "required" => true,
                "choices"  => [
                    'left' => "left",
                    'right' => "right",
                ]
            ])
            ->add("content", CKEditorType::class, [
                "required" => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add("media", EasyMediaType::class, [
                "required" => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])

        ;
    }


    public function getName(): string
    {
        return 'Text + Image';
    }

    public function getIcon(): string
    {
        return '<span class="fas fa-photo-video"></span>';
    }

    public function getTemplate(): string
    {
        return "@EasyEditor/block/media.html.twig";
    }
}
