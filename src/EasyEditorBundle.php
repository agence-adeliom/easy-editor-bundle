<?php

namespace Adeliom\EasyEditorBundle;

use Adeliom\EasyEditorBundle\DependencyInjection\EasyEditorExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EasyEditorBundle extends Bundle
{
    /**
     * @return ExtensionInterface|null The container extension
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new EasyEditorExtension();
    }
}
