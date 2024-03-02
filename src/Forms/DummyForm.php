<?php

namespace Andreapozza\PrestashopLegacyModuleForm\Forms;

use Andreapozza\PrestashopLegacyModuleForm\Configuration;
use Andreapozza\PrestashopLegacyModuleForm\Fields\SwitchField;
use Andreapozza\PrestashopLegacyModuleForm\Forms\AbstractConfigurationForm;

class DummyForm extends AbstractConfigurationForm
{
    protected function fields()
    {
        // yield SwitchField::make(Configuration::DUMMY, $this->module->l('Dummy switch', 'DummyForm'))->toArray();

        // yield [
        //     'type' => 'text',
        //     'label' => $this->module->l('Dummy input', 'DummyForm'),
        //     'name' => Configuration::DUMMY,
        //     'required' => true
        // ];
    }
}
