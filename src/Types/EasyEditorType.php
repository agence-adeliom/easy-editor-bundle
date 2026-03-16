<?php

declare(strict_types=1);

namespace Adeliom\EasyEditorBundle\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

final class EasyEditorType extends JsonType
{
    public const EASYEDITORTYPE = 'easy_editor_type';

    public function getName(): string
    {
        return self::EASYEDITORTYPE;
    }
}
