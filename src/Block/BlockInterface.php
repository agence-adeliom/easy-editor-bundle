<?php


namespace Adeliom\EasyEditorBundle\Block;


use Symfony\Component\Form\FormBuilderInterface;

interface BlockInterface
{
    public function getName(): string;
    public function getIcon(): string;
    public function getTemplate(): string;

}
