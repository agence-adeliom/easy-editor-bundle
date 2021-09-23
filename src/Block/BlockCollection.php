<?php


namespace Adeliom\EasyEditorBundle\Block;


class BlockCollection
{
    /** @var BlockInterface[] */
    protected array $blocks;

    public function __construct(iterable $blocks)
    {
        foreach($blocks as $block) {
            $this->blocks[get_class($block)] = $block;
        }
    }


    public function getBlocks(): array
    {
        return $this->blocks;
    }
}
