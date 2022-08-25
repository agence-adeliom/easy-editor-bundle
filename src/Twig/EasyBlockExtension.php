<?php

namespace Adeliom\EasyEditorBundle\Twig;

use Adeliom\EasyEditorBundle\Block\Helper;
use Twig\Extension\AbstractExtension;
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
