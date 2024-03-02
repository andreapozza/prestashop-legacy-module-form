<?php

namespace Andreapozza\PrestashopLegacyModuleForm\Forms;

use Tools;
use AdminController;
use Andreapozza\PrestashopLegacyModuleForm\Configuration;
use Andreapozza\PrestashopLegacyModuleForm\Forms\AbstractBaseForm;
use Module;

abstract class AbstractConfigurationForm extends AbstractBaseForm
{
    protected function afterFieldValidation($field, $value, $id_lang = null)
    {
        if ($id_lang) {
            $value = [$id_lang => $value];
        }
        Configuration::updateValue($field['name'], $value);
    }

    protected function getTable()
    {
        return 'module';
    }

    protected function getToken()
    {
        return Tools::getAdminTokenLite('AdminModules');
    }

    protected function getCurrentIndex()
    {
        return AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->module->name]);
    }

    protected function setModule()
    {
        $module_name = explode(DIRECTORY_SEPARATOR, str_replace(_PS_MODULE_DIR_, '', __DIR__))[0];
        $this->module = Module::getInstanceByName($module_name);
    }
}
