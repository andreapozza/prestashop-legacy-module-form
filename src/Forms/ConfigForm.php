<?php

namespace Andreapozza\PrestashopLegacyModuleForm\Forms;

use Andreapozza\PrestashopLegacyModuleForm\Configuration;
use Andreapozza\PrestashopLegacyModuleForm\Fields\SwitchField;
use Andreapozza\PrestashopLegacyModuleForm\Forms\AbstractConfigurationForm;

class ConfigForm extends AbstractConfigurationForm
{
    protected function fields()
    {
        yield SwitchField::make(Configuration::SEND_EMAIL, $this->module->l('Send email', 'ConfigForm'))->toArray();

        if (Configuration::getSendEmail() == '1') {
            yield [
                'type' => 'text',
                'label' => $this->module->l('Mail to', 'ConfigForm'),
                'name' => Configuration::MAIL_TO,
                'required' => true
            ];

            yield [
                'type' => 'text',
                'label' => $this->module->l('Mail bcc', 'ConfigForm'),
                'name' => Configuration::MAIL_BCC,
                'required' => false
            ];

            yield [
                'type' => 'text',
                'label' => $this->module->l('Mail subject', 'ConfigForm'),
                'name' => Configuration::MAIL_SUBJECT,
                'required' => true,
                'lang' => true
            ];
        }
    }
}
