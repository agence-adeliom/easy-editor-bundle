<?php

namespace Adeliom\EasyEditorBundle\Block;

use Doctrine\Common\Collections\ArrayCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;

class BlockCollection
{
    /** @var BlockInterface[] */
    protected $blocks = [];

    protected ?EntityDto $entityDto = null;

    public function __construct(iterable $blocks)
    {
        foreach ($blocks as $block) {
            $this->blocks[$block::class] = $block;
        }

        uasort($this->blocks, static fn ($a, $b) => $a->getPosition() <=> $b->getPosition());
        $this->blocks = new ArrayCollection($this->blocks);
    }

    public function enabledSupportFilter(EntityDto $entityDto)
    {
        $this->entityDto = $entityDto;
        $this->filterSupportedBlocks();

        return $this;
    }

    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * @param array $blockTypes
     *
     * @return array
     */
    public function getAllowedBlocks(?array $blockTypes)
    {
        $blocks = $this->getBlocks();

        if (empty($blockTypes)) {
            return $blocks;
        }

        return $blocks->filter(static fn (BlockInterface $block, $type) => in_array($type, $blockTypes));
    }

    private function filterSupportedBlocks(): void
    {
        if (null !== $this->entityDto) {
            $this->blocks = $this->blocks->filter(fn (BlockInterface $block, $type) => $block->supports($this->entityDto->getFqcn(), $this->entityDto->getInstance()));
        }
    }
}
