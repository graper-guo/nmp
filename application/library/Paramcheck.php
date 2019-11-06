<?php

use Nos\Exception\CoreException;

class Paramcheck
{
    /**
     * 校验类型
     */
    public static $validParameterTypeList = [
        'number', 'string', 'email', 'phone', 'date', 'datetime', 'json', 'array', 'enum', 'sequence','noEmptyString'
    ];

    /**
     * @param array $data       ['name'=>'xiao','age'=>19]
     * @param array $schema     array(
     *                          'ids' => ['type' => 'array', 'is_allow_empty' => true, 'errorMsg' => 'ids不能为空'],
     *                          'name'=>['type'=>'string','min'=>1,'max'=>10,'defaultV'=>'','errorMsg'=>''],
     *                          'age'=>['type'=>'number','min'=>1,'max'=>10,'defaultV'=>0,'errorMsg'=>''],
     *                          'status'=>['type'=>'enum','defaultV'=>0,'items'=>[1,2,3],'errorMsg'=>''],
     *                          'project_names' => ['type' => 'json', 'max' => 30, 'defaultV' => [], 'errorMsg' => 'project_names一次最多请求30个'],
     *                          'project_names' => ['type' => 'array', 'max' => 30, 'defaultV' => [], 'errorMsg' => 'project_names一次最多请求30个'],
     *                          'project_names' => ['type' => 'sequence', 'max' => 30, 'defaultV' => [], 'errorMsg' => 'project_names一次最多请求30个'],
     *                          );
     * @return mixed
     */
    public static function check($data, $schema)
    {
        $schema = self::standardize($schema);
        $result = array();
        foreach ($schema as $key => $item) {
            if (!isset($data[$key])) {
                if (!array_key_exists('defaultV', $item)) {
                    $item['errorMsg'] = isset($item['errorMsg']) ? $item['errorMsg'] : 'params ' . $key . ' is missing';
                    self::handleException($item);
                } else {
                    $result[$key] = $item['defaultV'];
                    continue;
                }
            }

            if (isset($data[$key]) && isset($item['defaultV']) && $data[$key] == $item['defaultV']) {
                $result[$key] = $item['defaultV'];
                continue;
            }

            $methodString = 'check' . ucwords($item['type']);
            $result[$key] = self::$methodString($key, $data[$key], $item);
        }
        return $result;
    }

    /**
     * 标准化校验模板
     * @param $schema
     * @return array
     * @throws \Exception
     */
    private static function standardize($schema)
    {
        foreach ($schema as $key => $item) {
            if (!isset($schema[$key]['type']) || !in_array($schema[$key]['type'], self::$validParameterTypeList)) {
                if(!isset($item['errorMsg'])){
                    $item['errorMsg'] = 'Parameter check type is error! ';
                }
                self::handleException($item);
            }
        }
        return $schema;
    }

    /**
     * @return int|string
     * @throws \Exception
     */
    private static function checkNumber()
    {
        list ($key, $value, $item) = func_get_args();
        if (is_numeric($value)) {
            if (isset($item['min'])) {
                if (intval($value) < intval($item['min'])) {
                    $error_msg = isset($item['errorMsg']) ? $item['errorMsg'] : sprintf('Param %s min value is %d', $key, $item['min']);
                    self::handleException($item, $key, $error_msg);
                }
            }
            if (isset($item['max'])) {
                if (intval($value) > intval($item['max'])) {
                    $error_msg = isset($item['errorMsg']) ? $item['errorMsg'] : sprintf('Param %s max value is %d', $key, $item['max']);
                    self::handleException($item, $key, $error_msg);
                }
            }
            return self::integerVal($value);
        } else {
            self::handleException($item, $key);
        }
    }

    /**
     * 将数据转化为整数,转化失败返回0
     * @param $value
     * @return int
     */
    public static function integerVal($value)
    {
        $value  = trim($value);
        $result = preg_match("/^[+-]?[1-9][0-9]*$/", $value, $m);
        if ($result && count($m) > 0) {
            return intval($m[0]);
        }
        return 0;
    }


    /**
     * @return Non empty string
     * @throws \Exception
     */
    private static function checkNoEmptyString()
    {
        $argv = func_get_args();
        $item = $argv[2];
        if (is_string($argv[1]) && $argv[1] != '') {
            return htmlspecialchars(htmlspecialchars_decode(trim($argv[1])));
        } else {
            self::handleException($item, $argv[0]);
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    private static function checkString()
    {
        list ($key, $value, $item) = func_get_args();
        if (is_string($value)) {
            if (isset($item['min'])) {
                if (mb_strlen($value, 'UTF-8') < intval($item['min'])) {
                    $error_msg = isset($item['errorMsg']) ? $item['errorMsg'] : sprintf('Param %s min length is %d', $key, $item['min']);
                    self::handleException($item, $key, $error_msg);
                }
            }
            if (isset($item['max'])) {
                if (mb_strlen($value, 'UTF-8') > intval($item['max'])) {
                    $error_msg = isset($item['errorMsg']) ? $item['errorMsg'] : sprintf('Param %s max length is %d', $key, $item['max']);
                    self::handleException($item, $key, $error_msg);
                }
            }
            return htmlspecialchars(htmlspecialchars_decode(trim($value)));
        } else {
            self::handleException($item, $key);
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private static function checkEmail()
    {
        $argv   = func_get_args();
        $item   = $argv[2];
        $result = filter_var($argv[1], FILTER_VALIDATE_EMAIL);
        if ($result !== false) {
            return trim($argv[1]);
        } else {
            self::handleException($item, $argv[0]);
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private static function checkPhone()
    {
        $argv    = func_get_args();
        $item    = $argv[2];
        $pattern = '/^\+?[0-9]{0,15}$/';
        if (preg_match($pattern, $argv[1])) {
            return trim($argv[1]);
        } else {
            self::handleException($item, $argv[0]);
        }
    }

    /**
     * 日期校验
     * @return bool
     * @throws \Exception
     */
    private static function checkDate()
    {
        $argv = func_get_args();
        $item = $argv[2];
        if (strtotime(date('Y-m-d', strtotime($argv[1]))) === strtotime($argv[1])) {
            return trim($argv[1]);
        } else {
            self::handleException($item, $argv[0]);
        }
    }

    /**
     * 日期时间校验
     * @return bool
     * @throws \Exception
     */
    private static function checkDatetime()
    {
        $argv = func_get_args();
        $item = $argv[2];
        if (strtotime(date('Y-m-d H:i:s', strtotime($argv[1]))) === strtotime($argv[1])) {
            return trim($argv[1]);
        } else {
            self::handleException($item, $argv[0]);
        }
    }

    /**
     * 检测是否为json串
     * @return mixed
     */
    private static function checkJson()
    {
        $argv  = func_get_args();
        $value = $argv[1];
        $item  = $argv[2];
        if (is_string($value)) {
            $value = json_decode($value, true);
            //兼容php7下json_decode空字符串是json_last_error不为0到问题
            if (json_last_error() == JSON_ERROR_NONE || $argv[1] === '') {
                if (isset($item['max'])) {
                    if (count($value) > intval($item['max'])) {
                        $error_msg = isset($item['errorMsg']) ? $item['errorMsg'] : sprintf('Param %s max length is %d', $argv[0], $item['max']);
                        self::handleException($item, $argv[0], $error_msg);
                    }
                }
                return $value;
            } else {
                self::handleException($argv[3], $argv[0]);
            }
        } else {
            self::handleException($item, $argv[0]);
        }
    }

    /**
     * 检测是否为数组
     * @return mixed
     */
    private static function checkArray()
    {
        $argv  = func_get_args();
        $value = $argv[1];
        $item  = $argv[2];
        if (is_string($value)) {
            $value = json_decode($value, true);
            //兼容php7下json_decode空字符串是json_last_error不为0到问题
            if (json_last_error() != JSON_ERROR_NONE && $argv[1] !== '') {
                self::handleException($item, $argv[0]);
            }
        }
        if (is_array($value)) {
            if (isset($item['max'])) {
                if (count($value) > intval($item['max'])) {
                    $error_msg = isset($item['errorMsg']) ? $item['errorMsg'] : sprintf('Param %s max length is %d', $argv[0], $item['max']);
                    self::handleException($item, $argv[0], $error_msg);
                }
            }
            if (isset($item['is_allow_empty']) && !$item['is_allow_empty'] && empty($value)) {
                $error_msg = isset($item['errorMsg']) ? $item['errorMsg'] : sprintf('Param %s is empty', $argv[0]);
                self::handleException($item, $argv[0], $error_msg);
            }
            return $value;
        } else {
            self::handleException($item, $argv[0]);
        }
    }

    /**
     * 检测枚举类型
     * @return mixed
     */
    private static function checkEnum()
    {
        $argv  = func_get_args();
        $value = $argv[1];
        $item  = $argv[2];
        if (isset($item['items']) && is_array($item['items']) && in_array($value, $item['items'])) {
            $key = array_search($value, $item['items']);
            return $item['items'][$key];
        } else {
            self::handleException($item, $argv[0]);
        }
    }

    /**
     * 检测批量序列类型，支持字符串和数组(post时)
     * @return mixed
     */
    private static function checkSequence()
    {
        $argv  = func_get_args();
        $value = $argv[1];
        $item  = $argv[2];
        if (!isset($item['max'])) {
            self::handleException($item, $argv[0], 'You should set sequence max config in your schema.');
        }
        if (is_string($value)) {
            $separator = ','; // default sequence is comma.
            if (isset($item['separator'])) {
                $separator = $item['separator'];
            }
            $array_from_sequence = array_unique(array_filter(explode($separator, $value)));
            if (count($array_from_sequence) > intval($item['max'])) {
                $error_msg = isset($item['errorMsg']) ? $item['errorMsg'] : sprintf('Param %s max length is %d', $argv[0], $item['max']);
                self::handleException($item, $argv[0], $error_msg);
            }
            return $array_from_sequence;
        } else {
            self::handleException($item, $argv[0]);
        }
    }

    /**
     * 处理参数错误异常
     * @param $item
     * @param $key
     * @param $error_msg
     * @throws \Exception
     */
    private static function handleException($item, $key = '', $error_msg = '')
    {
        if ($error_msg === '') {
            if (isset($item['errorMsg'])) {
                $error_msg = $item['errorMsg'];
            } else {
                $error_msg = sprintf('param %s is invalid!', $key);
            }
        }
        throw new CoreException('参数错误'.$error_msg);
    }

}
