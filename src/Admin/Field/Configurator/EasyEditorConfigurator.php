<?php

namespace Adeliom\EasyEditorBundle\Admin\Field\Configurator;

use Adeliom\EasyEditorBundle\Admin\Field\EasyEditorField;
use Adeliom\EasyEditorBundle\Block\BlockCollection;
use Doctrine\ORM\PersistentCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use function Symfony\Component\String\u;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class EasyEditorConfigurator implements FieldConfiguratorInterface
{

    protected BlockCollection $collection;

    public function __construct(BlockCollection $collection)
    {
        $this->collection = $collection;
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return EasyEditorField::class === $field->getFieldFqcn();
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        if (null !== $entryTypeFqcn = $field->getCustomOptions()->get(EasyEditorField::OPTION_ENTRY_TYPE)) {
            $field->setFormTypeOption('entry_type', $entryTypeFqcn);
        }

        $autocompletableFormTypes = [CountryType::class, CurrencyType::class, LanguageType::class, LocaleType::class, TimezoneType::class];
        if (\in_array($entryTypeFqcn, $autocompletableFormTypes, true)) {
            $field->setFormTypeOption('entry_options.attr.data-ea-widget', 'ea-autocomplete');
        }

        $field->setFormTypeOption('allow_drag', $field->getCustomOptions()->get(EasyEditorField::OPTION_ALLOW_DRAG));
        $field->setFormTypeOption('allow_add', $field->getCustomOptions()->get(EasyEditorField::OPTION_ALLOW_ADD));
        $field->setFormTypeOption('allow_delete', $field->getCustomOptions()->get(EasyEditorField::OPTION_ALLOW_DELETE));
        $field->setFormTypeOptionIfNotSet('by_reference', false);
        $field->setFormTypeOptionIfNotSet('delete_empty', true);

        $blocksCollection = $this->collection->enabledSupportFilter($entityDto);
        $blocks = $blocksCollection->getAllowedBlocks($field->getCustomOptions()->get(EasyEditorField::OPTION_BLOCKS));

        foreach ($blocks as $blockType => $block){
            if (method_exists($blockType,'configureAdminAssets')){
                $assets = call_user_func([$blockType,'configureAdminAssets']);
                if(!empty($assets['js'])){
                    foreach ($assets['js'] as $file){
                        $found = false;
                        foreach ($context->getAssets()->getJsAssets() as $assetDto) {
                            if ($assetDto->getValue() === $file) {
                                $found = true;
                            }
                        }
                        if (!$found) {
                            $context->getAssets()->addJsAsset(new AssetDto($file));
                        }
                    }
                }
                if(!empty($assets['css'])){
                    foreach ($assets['css'] as $file){
                        $found = false;
                        foreach ($context->getAssets()->getCssAssets() as $assetDto) {
                            if ($assetDto->getValue() === $file) {
                                $found = true;
                            }
                        }
                        if (!$found) {
                            $context->getAssets()->addCssAsset(new AssetDto($file));
                        }
                    }
                }
            }

            if (method_exists($blockType,'configureAdminFormTheme')){
                $formThemes = call_user_func([$blockType,'configureAdminFormTheme']);
                if(!empty($formThemes) && $context->getCrud()){
                    $context->getCrud()->setFormThemes(array_merge($context->getCrud()->getFormThemes(), $formThemes));
                }
            }
        }

        $field->setFormTypeOption('blocks', $blocks->toArray());

        // TODO: check why this label (hidden by default) is not working properly
        // (generated values are always the same for all elements)
        $field->setFormTypeOptionIfNotSet('entry_options.label', $field->getCustomOptions()->get(EasyEditorField::OPTION_SHOW_ENTRY_LABEL));

        // collection items range from a simple <input text> to a complex multi-field form
        // the 'entryIsComplex' setting tells if the collection item is so complex that needs a special
        // rendering not applied to simple collection items
        if (null === $field->getCustomOption(EasyEditorField::OPTION_ENTRY_IS_COMPLEX)) {
            $definesEntryType = null !== $entryTypeFqcn = $field->getCustomOption(EasyEditorField::OPTION_ENTRY_TYPE);
            $isSymfonyCoreFormType = null !== u($entryTypeFqcn ?? '')->indexOf('Symfony\Component\Form\Extension\Core\Type');
            $isComplexEntry = $definesEntryType && !$isSymfonyCoreFormType;

            $field->setCustomOption(EasyEditorField::OPTION_ENTRY_IS_COMPLEX, $isComplexEntry);
        }

        $field->setFormattedValue($this->formatCollection($field, $context));
    }

    private function formatCollection(FieldDto $field, AdminContext $context)
    {
        $doctrineMetadata = $field->getDoctrineMetadata();
        if ('array' !== $doctrineMetadata->get('type') && !$field->getValue() instanceof PersistentCollection) {
            return $this->countNumElements($field->getValue());
        }

        $collectionItemsAsText = [];
        foreach ($field->getValue() ?? [] as $item) {
            if (!\is_string($item) && !(\is_object($item) && method_exists($item, '__toString'))) {
                return $this->countNumElements($field->getValue());
            }

            $collectionItemsAsText[] = (string) $item;
        }

        $isDetailAction = Action::DETAIL === $context->getCrud()->getCurrentAction();

        return u(', ')->join($collectionItemsAsText)->truncate($isDetailAction ? 512 : 32, '…')->toString();
    }

    private function countNumElements($collection): int
    {
        if (null === $collection) {
            return 0;
        }

        if (is_countable($collection)) {
            return \count($collection);
        }

        if ($collection instanceof \Traversable) {
            return iterator_count($collection);
        }

        return 0;
    }
}
