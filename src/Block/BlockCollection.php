<?php

namespace Adeliom\EasyEditorBundle\Block;

use Doctrine\Common\Collections\ArrayCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;

class BlockCollection
{
    /** @var ArrayCollection<BlockInterface>|array */
    protected array|ArrayCollection $blocks = [];

    protected ?EntityDto $entityDto = null;

    public function __construct(iterable $blocks)
    {
        foreach ($blocks as $block) {
            $this->blocks[$block::class] = $block;
        }

        uasort($this->blocks, static fn ($a, $b) => $a->getPosition() <=> $b->getPosition());
        $this->blocks = new ArrayCollection($this->blocks);
    }

    public function enabledSupportFilter(EntityDto $entityDto): static
    {
        $this->entityDto = $entityDto;
        $this->filterSupportedBlocks();

        return $this;
    }

    /** @return ArrayCollection<BlockInterface>|array */
    public function getBlocks(): ArrayCollection|array
    {
        return $this->blocks;
    }

    /**
     * @param array<string> $blockTypes
     */
    public function getAllowedBlocks(?array $blockTypes): ArrayCollection|array
    {
        $blocks = $this->getBlocks();

        if ($blockTypes === null || $blockTypes === []) {
            return $blocks;
        }

        return $blocks->filter(static fn (BlockInterface $block, $type) => in_array($type, $blockTypes));
    }

    private function filterSupportedBlocks(): void
    {
        if ($this->entityDto instanceof \EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto) {
            $this->blocks = $this->blocks->filter(fn (BlockInterface $block, $type) => $block->supports($this->entityDto->getFqcn(), $this->entityDto->getInstance()));
        }
    }
}
