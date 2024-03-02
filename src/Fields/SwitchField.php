<?php

namespace Andreapozza\PrestashopLegacyModuleForm\Fields;

use FormField;

class SwitchField extends FormField
{
    private $description;

    public function __construct($name, $label = '')
    {
        $this->setType('switch');
        $this->setName($name);
        $this->setLabel($label);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['desc'] = $this->getDescription();
        $array['values'] = [
            ['value' => 1, 'id' => $this->getName() . '_on' ],
            ['value' => 0, 'id' => $this->getName() . '_off' ],
        ];
        return $array;
    }

    public static function make($name, $label = '')
    {
        return new self($name, $label);
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
}