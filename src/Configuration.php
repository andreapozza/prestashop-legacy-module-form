<?php

namespace Andreapozza\PrestashopLegacyModuleForm;

use Configuration as ConfigurationCore;

/**
 * @method static string getDummy($idLang = null, $idShopGroup = null, $idShop = null, $default = false)
 */
class Configuration extends ConfigurationCore
{
    const DUMMY = 'DUMMY';

    public static function __callStatic($method, $arguments)
    {
        if (preg_match('/^get(.+)/', $method, $matches)) {
            $config = strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $matches[1]));
            $class = get_called_class();
            $constant = $class.'::'.$config;
            if (! defined($constant)) {
                $class = self::class;
                $constant = $class.'::'.$config;
            }
            if (defined($constant)) {
                array_unshift($arguments, constant($constant));
                return call_user_func_array([$class, 'get'], $arguments);
            }
        }
    }

}