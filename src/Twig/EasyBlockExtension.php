<?php

namespace Adeliom\EasyEditorBundle\Twig;

use Adeliom\EasyEditorBundle\Block\BlockCollection;
use Adeliom\EasyEditorBundle\Block\Helper;
use Adeliom\EasyEditorBundle\Editor\Editor;
use Adeliom\EasyEditorBundle\Editor\EditorConfig;
use Adeliom\EasyEditorBundle\Editor\EditorConfigCollection;
use Adeliom\EasyEditorBundle\Form\EditorjsTransformer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

class EasyBlockExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('easy_editor_block', [Helper::class, 'renderEasyEditorBlock'], ['is_safe' => ['js', 'html'], 'needs_context' => true, 'needs_environment' => true]),
            new TwigFunction('easy_editor_assets', [Helper::class, 'includeAssets'], ['is_safe' => ['js', 'html'], 'needs_context' => true, 'needs_environment' => true]),
        ];
    }

}
