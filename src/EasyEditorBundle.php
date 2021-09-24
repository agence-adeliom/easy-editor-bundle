<?php

namespace Adeliom\EasyEditorBundle;

use Adeliom\EasyEditorBundle\DependencyInjection\EasyEditorExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EasyEditorBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new EasyEditorExtension();
    }
}
