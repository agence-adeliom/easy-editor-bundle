<?php

namespace Adeliom\EasyEditorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Adeliom\EasyEditorBundle\DependencyInjection\EasyEditorExtension;

class EasyEditorBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new EasyEditorExtension();
    }
}
