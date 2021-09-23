<?php

namespace Adeliom\EasyEditorBundle\Twig;

use Adeliom\EasyEditorBundle\Block\AbstractBlock;
use Adeliom\EasyEditorBundle\Block\BlockCollection;
use Adeliom\EasyEditorBundle\Editor\Editor;
use Adeliom\EasyEditorBundle\Editor\EditorConfig;
use Adeliom\EasyEditorBundle\Editor\EditorConfigCollection;
use Adeliom\EasyEditorBundle\Form\EditorjsTransformer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

class EasyBlockExtension extends AbstractExtension
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var BlockCollection
     */
    private $collection;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(Environment $twig, EventDispatcherInterface $eventDispatcher, BlockCollection $collection)
    {
        $this->twig = $twig;
        $this->collection = $collection;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_easy_editor_block', [$this, 'renderEasyEditorBlock'], ['is_safe' => ['js', 'html'], 'needs_context' => true, 'needs_environment' => true]),
        ];
    }

    /**
     * @param array $datas
     */
    public function renderEasyEditorBlock(Environment $env, array $context, $datas, $extra = [])
    {
        $block = $this->collection->getBlocks()[$datas["block_type"]];

        $event = new GenericEvent(null, ['datas' => $datas, "block" => $block ]);
        /**
         * @var GenericEvent $result;
         */
        $result = $this->eventDispatcher->dispatch($event, "easy_editor.render_block");

        $block = $result->getArgument('block');
        $datas = $result->getArgument('datas');

        return new Markup($this->twig->render($block->getTemplate(), array_merge($context, [
            "block" => $datas
        ], $extra)), 'UTF-8');
    }

}
