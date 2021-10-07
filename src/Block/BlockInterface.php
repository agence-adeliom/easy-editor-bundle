<?php


namespace Adeliom\EasyEditorBundle\Block;


interface BlockInterface
{
    public function getName(): string;
    public function getIcon(): string;
    public function getTemplate(): string;
    public function configureAssets(): array;

}
