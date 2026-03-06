<?php

declare(strict_types=1);

namespace Adeliom\EasyEditorBundle\Event;

use Adeliom\EasyEditorBundle\Block\BlockInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class RenderBlockEvent extends Event
{
    /**
     * @param array<string, mixed> $settings
     * @param array<string, mixed> $assets
     */
    public function __construct(
        private BlockInterface $block,
        private array $settings,
        private array $assets,
    ) {
    }

    public function getBlock(): BlockInterface
    {
        return $this->block;
    }

    public function setBlock(BlockInterface $block): void
    {
        $this->block = $block;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array<string, mixed> $settings
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAssets(): array
    {
        return $this->assets;
    }

    /**
     * @param array<string, mixed> $assets
     */
    public function setAssets(array $assets): void
    {
        $this->assets = $assets;
    }
}
