<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use Adeliom\EasyEditorBundle\Block\AbstractBlock;
use Symfony\Component\Form\FormBuilderInterface;

class <?= $class_name; ?> extends AbstractBlock<?= "\n" ?>
{
    public function buildBlock(FormBuilderInterface $builder, array $options): void
    {
        // Implement with your fields
    }

    public function getName(): string
    {
        return '<?= $class_name; ?>';
    }

    public function getIcon(): string
    {
        return '';
    }

    public function getTemplate(): string
    {
        return "<?= $template_name ?>";
    }
}