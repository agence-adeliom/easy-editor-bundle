<?php

namespace Adeliom\EasyEditorBundle\Block;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractBlock extends AbstractType implements BlockInterface
{
    public function __construct(protected EntityManagerInterface $manager)
    {
    }

    public function getManager(): EntityManagerInterface
    {
        return $this->manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('block_type', HiddenType::class, ['data' => $this::class])
            ->add('position', HiddenType::class)
        ;

        $this->buildBlock($builder, $options);
    }

    abstract public function buildBlock(FormBuilderInterface $builder, array $options): void;

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attr = [];
        $attr['block-title'] = $this->getName();
        $attr['block-icon'] = is_iterable($this->getIcon()) ? $this->getIcon()[0] : $this->getIcon();
        $view->vars['attr'] = $attr;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'cascade_validation' => true,
        ]);
    }

    public static function configureAssets(): array
    {
        return [
            'js' => [],
            'css' => [],
            'webpack' => [],
        ];
    }

    public static function configureAdminAssets(): array
    {
        return [
            'js' => [],
            'css' => [],
        ];
    }

    public static function configureAdminFormTheme(): array
    {
        return [];
    }

    public static function indexableProperties(): array
    {
        return [];
    }

    public function getPosition(): int
    {
        return 100;
    }

    public function supports(string $objectClass, $instance = null): bool
    {
        return true;
    }
}
