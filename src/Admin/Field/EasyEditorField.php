<?php

namespace Adeliom\EasyEditorBundle\Admin\Field;

use Adeliom\EasyEditorBundle\Form\EditorCollectionType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class EasyEditorField implements FieldInterface
{
    use FieldTrait;

    /**
     * @var string
     */
    public const OPTION_ALLOW_DRAG = 'allowDrag';

    /**
     * @var string
     */
    public const OPTION_ALLOW_ADD = 'allowAdd';

    /**
     * @var string
     */
    public const OPTION_ALLOW_DELETE = 'allowDelete';

    /**
     * @var string
     */
    public const OPTION_ENTRY_IS_COMPLEX = 'entryIsComplex';

    /**
     * @var string
     */
    public const OPTION_ENTRY_TYPE = 'entryType';

    /**
     * @var string
     */
    public const OPTION_SHOW_ENTRY_LABEL = 'showEntryLabel';

    /**
     * @var string
     */
    public const OPTION_RENDER_EXPANDED = 'renderExpanded';

    /**
     * @var string
     */
    public const OPTION_BLOCKS = 'blocks';

    /**
     * @param string|false|null $label
     */
    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/collection')
            ->setFormType(EditorCollectionType::class)
            ->addCssClass('field-collection field-collection_sortable')
            ->addJsFiles('bundles/easyeditor/field-editor.js')
            ->addCssFiles(Asset::new('bundles/easyeditor/easy-editor.css')->onlyOnForms())
            ->setDefaultColumns('col-12')
            ->setCustomOption(self::OPTION_ALLOW_DRAG, true)
            ->setCustomOption(self::OPTION_ALLOW_ADD, true)
            ->setCustomOption(self::OPTION_ALLOW_DELETE, true)
            ->setCustomOption(self::OPTION_ENTRY_IS_COMPLEX, null)
            ->setCustomOption(self::OPTION_SHOW_ENTRY_LABEL, false)
            ->setCustomOption(self::OPTION_RENDER_EXPANDED, false);
    }

    public function allowDrag(bool $allow = true): self
    {
        $this->setCustomOption(self::OPTION_ALLOW_DRAG, $allow);

        return $this;
    }

    public function allowAdd(bool $allow = true): self
    {
        $this->setCustomOption(self::OPTION_ALLOW_ADD, $allow);

        return $this;
    }

    public function allowDelete(bool $allow = true): self
    {
        $this->setCustomOption(self::OPTION_ALLOW_DELETE, $allow);

        return $this;
    }

    /**
     * Set this option to TRUE if the collection items are complex form types
     * composed of several form fields (EasyAdmin applies a special rendering to make them look better).
     */
    public function setEntryIsComplex(bool $isComplex): self
    {
        $this->setCustomOption(self::OPTION_ENTRY_IS_COMPLEX, $isComplex);

        return $this;
    }

    public function showEntryLabel(bool $showLabel = true): self
    {
        $this->setCustomOption(self::OPTION_SHOW_ENTRY_LABEL, $showLabel);

        return $this;
    }

    public function renderExpanded(bool $renderExpanded = true): self
    {
        $this->setCustomOption(self::OPTION_RENDER_EXPANDED, $renderExpanded);

        return $this;
    }

    public function allowedBlocks(array $blocks): self
    {
        $this->setCustomOption(self::OPTION_BLOCKS, $blocks);

        return $this;
    }
}
