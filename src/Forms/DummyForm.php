<?php

namespace Andreapozza\PrestashopLegacyModuleForm\Forms;

use Andreapozza\PrestashopLegacyModuleForm\Configuration;
use Andreapozza\PrestashopLegacyModuleForm\Fields\SwitchField;
use Andreapozza\PrestashopLegacyModuleForm\Forms\AbstractConfigurationForm;

class DummyForm extends AbstractConfigurationForm
{
    protected function fields()
    {
        // yield SwitchField::make(Configuration::DUMMY, 'Dummy switch')->toArray();

        // yield [
        //     'type' => 'text',
        //     'label' => 'Dummy input',
        //     'name' => Configuration::DUMMY,
        //     'required' => true
        // ];
    }
}
