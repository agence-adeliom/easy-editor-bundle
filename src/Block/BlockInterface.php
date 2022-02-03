<?php


namespace Adeliom\EasyEditorBundle\Block;


interface BlockInterface
{
    public function getName(): string;
    public function getIcon(): string | array;
    public function getPosition(): int;
    public function getTemplate(): string;
    public static function configureAssets(): array;
    public static function configureAdminAssets(): array;
    public static function configureAdminFormTheme(): array;
    public function supports(string $objectClass, $instance = null): bool;
}
