<?php


namespace Adeliom\EasyEditorBundle\Block;


interface BlockInterface
{
    public function getName(): string;
    public function getIcon(): string;
    public function getTemplate(): string;
    public static function configureAssets(): array;
    public static function configureAdminAssets(): array;
    public function supports(string $objectClass, $instance = null): bool;
}
