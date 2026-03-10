<?php

namespace Adeliom\EasyEditorBundle\Block;

use Adeliom\EasyCommonBundle\Event\LegacyEventDispatcher;
use Doctrine\ORM\EntityManagerInterface;
use Adeliom\EasyEditorBundle\Event\RenderBlockEvent;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactory;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Markup;

class Helper
{
    /**
     * This property is a state variable holdings all assets used by the block for the current PHP request
     * It is used to correctly render the javascripts and stylesheets tags on the main layout.
     */
    private array $assets = [
        'js' => [],
        'css' => [],
        'webpack' => [],
    ];

    private array $traces = [];

    public function __construct(
        private readonly Environment $twig,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly BlockCollection $collection,
        private readonly FormFactory $formFactory,
        private readonly EntityManagerInterface $em
    ) {
    }

    /**
     * @return mixed[]|string
     */
    public function includeAssets(): array|string
    {
        $html = '';

        if (!empty($this->assets['css']) && !empty($this->assets['css'])) {
            $html .= "<style media='all'>";
            foreach ($this->assets['css'] as $stylesheet) {
                $html .= "\n".sprintf('@import url(%s);', $stylesheet);
            }

            $html .= "\n</style>";
        }

        if (!empty($this->assets['js'])) {
            foreach ($this->assets['js'] as $javascript) {
                $html .= "\n".sprintf('<script src="%s" type="text/javascript"></script>', $javascript);
            }
        }

        if (!empty($this->assets['webpack'])) {
            foreach ($this->assets['webpack'] as $webpack) {
                try {
                    $html .= "\n".$this->twig->createTemplate(sprintf("{{ encore_entry_link_tags('%s') }}", $webpack))->render();
                    $html .= "\n".$this->twig->createTemplate(sprintf("{{ encore_entry_script_tags('%s') }}", $webpack))->render();
                } catch (LoaderError|SyntaxError) {
                    $html .= '';
                }
            }
        }

        return $html;
    }

    /**
     * Returns the rendering traces.
     */
    public function getTraces(): array
    {
        return $this->traces;
    }

    private function startTracing(BlockInterface $block): array
    {
        return [
            'id' => uniqid(),
            'name' => $block->getName(),
            'type' => $block::class,
            'position' => null,
            'datas' => [],
            'assets' => [
                'js' => [],
                'css' => [],
                'webpack' => [],
            ],
        ];
    }

    private function stopTracing($id, array $stats): void
    {
        $this->traces[$id] = $stats;
    }

    /**
     * @param array $datas
     */
    public function renderEasyEditorBlock(Environment $env, array $context, $datas, $extra = [])
    {
        $block = $this->collection->getBlocks()[$datas['block_type']];
        $stats = $this->startTracing($block);
        $blockType = $datas['block_type'];
        $defaultAssets = call_user_func([$blockType, 'configureAssets']);

        $blockSettings = $datas;

        $result = LegacyEventDispatcher::dispatchGenericEvent(
            new RenderBlockEvent($block, $blockSettings, $defaultAssets),
            $this->eventDispatcher,
            'agence-adeliom/easy-editor-bundle',
            'easy_editor.render_block',
            static fn (RenderBlockEvent $event): GenericEvent => new GenericEvent(null, [
                'settings' => $event->getSettings(),
                'block' => $event->getBlock(),
                'assets' => $event->getAssets(),
            ]),
            static function (RenderBlockEvent $event, GenericEvent $legacyEvent): void {
                $event->setBlock($legacyEvent->getArgument('block'));
                $event->setSettings($legacyEvent->getArgument('settings'));
                $event->setAssets($legacyEvent->getArgument('assets'));
            }
        );

        $block = $result->getBlock();
        $blockDatas = $result->getSettings();

        if (isset($blockDatas['block_type'])) {
            unset($blockDatas['block_type']);
        }

        if (isset($blockDatas['position'])) {
            $stats['position'] = $blockDatas['position'];
            unset($blockDatas['position']);
        }

        // Add a way to automatically set an ID (base on loop index when the page is rendered)
        if (empty($blockDatas['attr_id'])) {
            global $blockLoopIndex;
            if (empty($blockLoopIndex)) {
                $blockLoopIndex = 0;
            }

            ++$blockLoopIndex;
            $blockDatas['attr_id'] = 'block-'.$blockLoopIndex;
        }

        $stats['settings'] = $blockDatas;
        $stats['assets'] = $result->getAssets();

        $this->assets = array_merge_recursive($this->assets, $stats['assets']);

        $this->stopTracing($stats['id'], $stats);

        return new Markup($this->twig->render($block->getTemplate(), array_merge($context, [
            'block' => $datas,
            'blockType' => $blockType,
            'settings' => $blockDatas,
        ], $extra)), 'UTF-8');
    }
}
