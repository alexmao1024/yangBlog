<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;


#[\Attribute] class FileManaged extends Constraint
{
    public $mimeTypes;

    public function __construct(array $mimeTypes, mixed $options = null, array $groups = null, mixed $payload = null)
    {
        $this->mimeTypes = $mimeTypes;
        parent::__construct($options, $groups, $payload);
    }

    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = '当前文件{{ name }}不允许上传，只允许上传图片.';
}
