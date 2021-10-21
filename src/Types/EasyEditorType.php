<?php
namespace Adeliom\EasyEditorBundle\Types;

use Doctrine\DBAL\Types\JsonType;

class EasyEditorType extends JsonType
{
    const EASYEDITORTYPE = 'easy_editor_type'; // modify to match your type name

    public function getName()
    {
        return self::EASYEDITORTYPE; // modify to match your constant name
    }
}
