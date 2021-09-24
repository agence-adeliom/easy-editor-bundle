<?php
namespace Adeliom\EasyEditorBundle\Blocks;

use Adeliom\EasyEditorBundle\Block\AbstractBlock;
use Adeliom\EasyMediaBundle\Form\EasyMediaType;
use Symfony\Component\Form\FormBuilderInterface;

class ImageType extends AbstractBlock
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add("media", EasyMediaType::class, [
                "required" => true,
            ])
        ;
    }

    public function getName(): string
    {
        return 'Media';
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
