<?php

namespace Adeliom\EasyEditorBundle\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class EasyEditorType extends JsonType
{
    /**
     * @var string
     */
    public const EASYEDITORTYPE = 'easy_editor_type';

    public function getName(): string
    {
        return self::EASYEDITORTYPE; // modify to match your constant name
    }

}
