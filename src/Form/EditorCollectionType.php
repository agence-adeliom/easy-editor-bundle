<?php

namespace Adeliom\EasyEditorBundle\Form;

use Adeliom\EasyEditorBundle\Block\BlockCollection;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditorCollectionType extends CollectionType
{

    public function __construct( BlockCollection $blockCollection ) {
        $this->blockCollection = $blockCollection;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['allow_add'] && $options['prototype']) {
            $prototypeOptions = array_replace([
                'required' => $options['required'],
                'label' => $options['prototype_name'].'label__',
            ], $options['entry_options']);

            if (null !== $options['prototype_data']) {
                $prototypeOptions['data'] = $options['prototype_data'];
            }
            $prototypeOptions['compound'] = true;
            $prototypeOptions['allow_extra_fields'] = true;

            $prototypes = [];
            
            foreach ($options['blocks'] as $type => $block){
                $name = sprintf('__block_%s__', $block->getBlockPrefix());
                if(!empty($prototypeOptions['label']) && str_contains("label__", prototypeOptions['label'])){
                    $prototypeOptions['label'] = $name . 'label__';
                }
                $form = $builder->create($name, get_class($block), $prototypeOptions);
                $prototypes[$type] = $form->getForm();
            }
            $builder->setAttribute('prototypes', $prototypes);
        }

        $resizeListener = new \Adeliom\EasyEditorBundle\EventListener\ResizeFormListener(
            $options['entry_type'],
            $options['entry_options'],
            $options['allow_add'],
            $options['allow_delete'],
            $options['delete_empty']
        );

        $builder->addEventSubscriber($resizeListener);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'allow_drag' => $options['allow_drag'],
            'allow_add' => $options['allow_add'],
            'allow_delete' => $options['allow_delete'],
            'blocks' => $options['blocks'],
        ]);
        if ($form->getConfig()->hasAttribute('prototypes')) {
            $prototypes = $form->getConfig()->getAttribute('prototypes');
            $view->vars['prototypes'] = [];
            foreach ($prototypes as $type => $prototype){
                $view->vars['prototypes'][$type] = $prototype->setParent($form)->createView($view);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $entryOptionsNormalizer = function (Options $options, $value) {
            $value['block_name'] = 'entry';

            return $value;
        };

        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'allow_drag' => false,
            'allow_add' => false,
            'allow_delete' => false,
            'prototype' => true,
            'prototypes' => [],
            'prototype_data' => null,
            'prototype_name' => '__name__',
            'entry_type' => TextType::class,
            'entry_options' => [],
            'delete_empty' => true,
            'by_reference' => false,
            'blocks' => $this->blockCollection->getBlocks()->toArray(),
            'invalid_message' => function (Options $options, $previousValue) {
                return ($options['legacy_error_messages'] ?? true)
                    ? $previousValue
                    : 'The collection is invalid.';
            },
        ]);

        $resolver->setNormalizer('entry_options', $entryOptionsNormalizer);
        $resolver->setAllowedTypes('delete_empty', ['bool', 'callable']);
        $resolver->setAllowedTypes('blocks', 'array');
        $resolver->setAllowedTypes('allow_drag', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $prefixOffset = -2;
        // check if the entry type also defines a block prefix
        /** @var FormInterface $entry */
        foreach ($form as $entry) {
            if ($entry->getConfig()->getOption('block_prefix')) {
                --$prefixOffset;
            }

            break;
        }

        foreach ($view as $entryView) {
            array_splice($entryView->vars['block_prefixes'], $prefixOffset, 0, 'editor_collection_entry');
        }

        /** @var FormInterface $prototype */
        if ($prototypes = $form->getConfig()->getAttribute('prototypes')) {
            foreach ($prototypes as $type => $prototype){
                if ($view->vars['prototypes'][$type]->vars['multipart']) {
                    $view->vars['multipart'] = true;
                }

                if ($prefixOffset > -3 && $prototype->getConfig()->getOption('block_prefix')) {
                    --$prefixOffset;
                }

                array_splice($view->vars['prototypes'][$type]->vars['block_prefixes'], $prefixOffset, 0, 'editor_collection_entry');
            }

        }
    }


    public function getBlockPrefix()
    {
        return 'editor_collection';
    }

}
