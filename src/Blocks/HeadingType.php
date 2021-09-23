<?php
namespace Adeliom\EasyEditorBundle\Blocks;

use Adeliom\EasyEditorBundle\Block\AbstractBlock;
use Adeliom\EasyEditorBundle\Block\BlockInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class HeadingType extends AbstractBlock
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add("type", ChoiceType::class, [
                "required" => true,
                "choices"  => [
                    'H1' => "h1",
                    'H2' => "h2",
                    'H3' => "h3",
                ]
            ])
            ->add("heading", TextType::class, [
                "required" => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 3])
                ],
            ])
        ;
    }


    public function getName(): string
    {
        return 'Heading';
    }

    public function getIcon(): string
    {
        return '<span class="fas fa-photo-video"></span>';
    }

    public function getTemplate(): string
    {
        return "@EasyEditor/block/heading.html.twig";
    }
}
