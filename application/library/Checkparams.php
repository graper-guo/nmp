<?php

class Checkparams
{
    /**
     * 过滤掉未传的参数
     *
     * 有些允许为空的参数，设其默认值为别的数据类型
     * 然后调用该函数可将这些参数过滤掉
     *
     * @param $params array 公共类检验过的参数map
     * @param $schema array service中定义的schema
     */
    public static function filterParams(&$params, $schema)
    {
        foreach ($params as $key => $val) {
            switch ($schema[$key]['type']) {
                case 'number':
                    if (!is_numeric($val)) {
                        unset($params[$key]);
                    }
                    break;
                case 'string':
                case 'date':
                case 'datetime':
                case 'phone':
                    if (!is_string($val)) {
                        unset($params[$key]);
                    }
                    break;
                case 'json':
                case 'array':
                    if (!is_array($val)) {
                        unset($params[$key]);
                    }
                    break;
                default:
                    break;
            }
        }
    }
}