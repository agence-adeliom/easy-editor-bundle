<?php

namespace Adeliom\EasyEditorBundle\Block;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Stopwatch\Stopwatch;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Markup;

class Helper
{

    /**
     * This property is a state variable holdings all assets used by the block for the current PHP request
     * It is used to correctly render the javascripts and stylesheets tags on the main layout.
     *
     * @var array
     */
    private $assets;

    /**
     * @var array
     */
    private $traces;

    /**
     * @var BlockCollection
     */
    private $collection;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var FormFactory
     */
    private $formFactory;

    public function __construct(Environment $twig, EventDispatcherInterface $eventDispatcher, BlockCollection $collection, FormFactory  $formFactory)
    {
        $this->twig = $twig;
        $this->collection = $collection;
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;

        $this->assets = [
            'js' => [],
            'css' => [],
            'webpack' => [],
        ];

        $this->traces = [];
    }

    /**
     * @return array|string
     */
    public function includeAssets()
    {
        $html = '';

        if (!empty($this->assets['css'])){
            $html .= "<style media='all'>";

            foreach ($this->assets['css'] as $stylesheet) {
                $html .= "\n".sprintf('@import url(%s);', $stylesheet);
            }

            $html .= "\n</style>";
        }

        foreach ($this->assets['js'] as $javascript) {
            $html .= "\n".sprintf('<script src="%s" type="text/javascript"></script>', $javascript);
        }

        foreach ($this->assets['webpack'] as $webpack) {
            try {
                $html .= "\n" . $this->twig->createTemplate(sprintf("{{ encore_entry_link_tags('%s') }}", $webpack))->render();
                $html .= "\n".$this->twig->createTemplate(sprintf("{{ encore_entry_script_tags('%s') }}", $webpack))->render();
            } catch (LoaderError | SyntaxError $e) {
                $html .= "";
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
            'type' => get_class($block),
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
        $block = $this->collection->getBlocks()[$datas["block_type"]];
        $stats = $this->startTracing($block);
        $blockType = $datas["block_type"];

        // Tranform settings way 1 : use blockType form transformers
        $blockSettings = $this->transformSettingsWithBlockTypeFormBuild($blockType, $block, $datas);

        // Tranform settings way 2 : with dispatch / event listeners
        $event = new GenericEvent(null, ['settings' => $blockSettings, "block" => $block, 'assets' => $block->configureAssets() ]);
        /**
         * @var GenericEvent $result;
         */
        $result = $this->eventDispatcher->dispatch($event, "easy_editor.render_block");


        $block = $result->getArgument('block');
        $blockDatas = $result->getArgument('settings');

        if(isset($blockDatas["block_type"])){
            unset($blockDatas["block_type"]);
        }

        if(isset($blockDatas["position"])){
            $stats["position"] = $blockDatas["position"];
            unset($blockDatas["position"]);
        }

        $stats["settings"] = $blockDatas;
        $stats["assets"] = $result->getArgument('assets');

        $this->assets = array_merge($this->assets, $stats["assets"]);

        $this->stopTracing($stats["id"], $stats);

        return new Markup($this->twig->render($block->getTemplate(), array_merge($context, [
            "block" => $block,
            "blockType" => $blockType,
            "settings" => $blockDatas,
        ], $extra)), 'UTF-8');
    }

    public function transformSettingsWithBlockTypeFormBuild($blockType, $block, $defaultSetting) {

        $formBuilder = $this->formFactory->createBuilder($block->getType(), null, ['csrf_protection' => false]);

        // init blockType form builder
        $blockType->buildBlock($formBuilder, []);

        // Submit to use optionnal form transformers
        $form = $formBuilder->getForm();
        $form->submit(array_merge($defaultSetting, $block->getSettings()));

        // Put norm datas into block settings
        // norm data are transfomed data
        $blockSettings = $form->getNormData();
        if (!empty($form->getNormData())) {
            foreach ($form->getNormData() as $field => $value) {
                if (!empty($form->get($field))) {
                    $blockSettings[$field] = $form->get($field)->getNormData();
                }
            }
        }

        return $blockSettings;
    }

}
