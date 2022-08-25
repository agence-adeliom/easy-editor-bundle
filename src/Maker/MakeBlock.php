<?php

namespace Adeliom\EasyEditorBundle\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

final class MakeBlock extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:block';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new block type';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConf)
    {
        $command
            ->addArgument('block-type', InputArgument::OPTIONAL, sprintf('Choose a name for your block type (e.g. <fg=yellow>%sType</>)', Str::asClassName(Str::getRandomTerm())))
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeBlock.txt'))
        ;
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $blockClassNameDetails = $generator->createClassNameDetails(
            $input->getArgument('block-type'),
            'Blocks\\',
            'Type'
        );
        $templateName = Str::asFilePath('blocks/'.$blockClassNameDetails->getRelativeNameWithoutSuffix()).'.html.twig';

        $blockPath = $generator->generateClass(
            $blockClassNameDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/block/Block.tpl.php',
            [
                'template_name' => $templateName,
            ]
        );

        $generator->generateTemplate(
            $templateName,
            __DIR__.'/../Resources/skeleton/block/twig_template.tpl.php',
            [
                'block_path' => $blockPath,
                'root_directory' => $generator->getRootDirectory(),
                'class_name' => $blockClassNameDetails->getShortName(),
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
        $io->text('Next: Open your new block type and add some fields!');
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
    }
}
