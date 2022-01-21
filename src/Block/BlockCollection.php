<?php


namespace Adeliom\EasyEditorBundle\Block;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;

class BlockCollection
{
    /** @var BlockInterface[] */
    protected $blocks;
    protected ?EntityDto $entityDto;

    public function __construct(iterable $blocks)
    {
        foreach($blocks as $block) {
            $this->blocks[get_class($block)] = $block;
        }
        $this->blocks = new ArrayCollection($this->blocks);
    }

    public function enabledSupportFilter(EntityDto $entityDto){
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
     * @return array
     */
    public function getAllowedBlocks(?array $blockTypes)
    {
        $blocks = $this->getBlocks();

        if(empty($blockTypes)){
            return $blocks;
        }

        return $blocks->filter(function (BlockInterface $block, $type) use ($blockTypes) {
            return in_array($type, $blockTypes);
        });
    }


    private function filterSupportedBlocks(): void
    {
        if($this->entityDto){
            $this->blocks = $this->blocks->filter(function (BlockInterface $block, $type) {
                return $block->supports($this->entityDto->getFqcn(), $this->entityDto->getInstance());
            });
        }
    }



}
