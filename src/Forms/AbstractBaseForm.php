<?php

namespace Andreapozza\PrestashopLegacyModuleForm\Forms;

use HelperForm;
use Tools;
use Configuration;
use Context;
use Andreapozza\PrestashopLegacyModuleForm\Exceptions\FieldNotValidException;
use Generator;
use Language;
use Module;

abstract class AbstractBaseForm
{
    /** @var Module */
    protected $module;
    /** @var array|Generator */
    private $fields;
    /** @var array */
    private $fieldsArray;
    /** @var Context */
    private $context;
    /** @var string */
    protected $title;
    /** @var string */
    protected $submit_action;
    /** @var string */
    private $token;
    /** @var string */
    private $currentIndex;
    /** @var string */
    private $table;

    public function __construct(string $title = '')
    {
        $this->setModule();
        $this->setTitle($title);
        $this->context = Context::getContext();
        $this->fields = $this->fields();
        $this->submit_action = $this->getSubmitAction();
        $this->token = $this->getToken();
        $this->currentIndex = $this->getCurrentIndex();
        $this->table = $this->getTable();
    }

    protected function getSubmitAction()
    {
        $default = 'submit' . $this->module->name . preg_replace('/\W/', '', $this->title);
        return isset($this->submit_action) ? $this->submit_action : $default;
    }

    abstract protected function setModule();

    abstract protected function getToken();

    abstract protected function getCurrentIndex();

    protected function getTable()
    {
        // optional
    }

    protected function setTitle()
    {
        if (empty($this->title) && func_num_args() > 0) {
            $this->title = func_get_arg(0);
        }
    }

    /** @return self */
    public static function make($title = '')
    {
        $class = get_called_class();
        return (new $class($title));
    }

    /**
     * @return array
     */
    abstract protected function fields();

    /**
     * Builds the configuration form
     * @return string HTML code
     */
    public function displayForm()
    {
        $error = '';
        $success = Tools::isSubmit($this->submit_action) ? $this->module->l('Saved', 'AbstractBaseForm') : '';
        try {
            $this->handleRequest();
        } catch (FieldNotValidException $e) {
            $error .= $e->getMessage();
            $success = '';
        }
        // Init Fields form array
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->title,
                ],
                'input' => $this->getFieldsArray(),
                'submit' => [
                    'title' => $this->module->getTranslator()->trans('Save', [], 'Admin.Global'),
                    'class' => 'btn btn-default pull-right',
                ],
                'error' => $error,
                'success' => $success
                
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        if (! $this->table) {
            $helper->table = $this->table;
        }
        $helper->name_controller = $this->module->name;
        $helper->token = $this->token;
        $helper->currentIndex = $this->currentIndex;
        $helper->submit_action = $this->submit_action;

        // Default language
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        // Load current value into the form
        $helper->fields_value = $this->getConfigValues($this->getFieldsArray());

        $helper->tpl_vars = array(
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm([$form]);
    }

    public function getFieldsArray()
    {
        if ($this->fields instanceof Generator) {
            return iterator_to_array($this->fields());
        }
        return $this->fields;
    }

    protected function getConfigValues($fields)
    {
        Configuration::loadConfiguration();
        return array_reduce($fields ?? [], function($carry, $item) {
            if (array_key_exists('lang', $item) && $item['lang']) {
                $values = Configuration::getConfigInMultipleLangs($item['name']);
                return array_merge($carry, [ $item['name'] => $values ]);
            }
            return array_merge($carry, [
                $item['name'] => Tools::getValue($item['name'], Configuration::get($item['name']))
            ]);
        }, []);
    }

    public function handleRequest()
    {
        foreach ($this->fields ?? [] as $field) {
            if (! Tools::isSubmit($field['name']) && ! $this->isFieldMultiLang($field)) {
                continue;
            }
            if ($this->isFieldMultiLang($field)) {
                foreach (Language::getIDs() as $id_lang) {
                    $field_name = $field['name'] . "_$id_lang";
                    if (! Tools::isSubmit($field_name)) {
                        continue;
                    }
                    $value = Tools::getValue($field_name);
                    $this->validateField($field, $value);
                    $this->afterFieldValidation($field, $value, $id_lang);
                }
                continue;
            }
            $value = Tools::getValue($field['name']);
            $this->validateField($field, $value);
            $this->afterFieldValidation($field, $value);
        }
        $this->postValidation();
    }

    /**
     * Put here your logic to excecute after each field validation
     *
     * @param array $field
     * @param mixed $value
     * @param mixed $id_lang used for multilang fields
     * @return void
     */
    protected function afterFieldValidation($field, $value, $id_lang = null)
    {
        //
    }

    /**
     * Put here your logic to excecute after entire form validation
     *
     * @return void
     */
    protected function postValidation()
    {
        //
    }

    private function validateField($field, $value)
    {
        if ($this->isFieldRequired($field) && empty($value)) {
            $message = strtr(':field is required', [':field' => $field['label']]);
            throw new FieldNotValidException($message);
        }
        $validation = $this->getValidationResult($field, $value);
        if (! $validation || is_string($validation)) {
            $message = is_string($validation) ? $validation : strtr(':field not valid', [':field' => $field['label']]);
            throw new FieldNotValidException($message);
        }
    }

    private function isFieldRequired($field)
    {
        return array_key_exists('required', $field) && $field['required'];
    }

    private function isFieldMultiLang($field)
    {
        return array_key_exists('lang', $field) && $field['lang'];
    }

    private function getValidationResult($field, $value)
    {
        if (array_key_exists('validation', $field) && is_callable($field['validation'])) {
            $callable = $field['validation'];
            return $callable($value);
        }

        return true;
    }
}
