<?php

namespace Adeliom\EasyEditorBundle\Block;

interface BlockInterface
{
    public function getName(): string;

    /**
     * @return string|mixed[]
     */
    public function getIcon(): string|array;

    public function getPosition(): int;

    public function getTemplate(): string;

    /**
     * @return array<string, string[]>
     */
    public static function configureAssets(): array;

    /**
     * @return array<string, string[]>
     */
    public static function configureAdminAssets(): array;

    /**
     * @return string[]
     */
    public static function configureAdminFormTheme(): array;

    /**
     * @return string[]
     */
    public static function indexableProperties(): array;

    public function supports(string $objectClass, ?object $instance = null): bool;
}
