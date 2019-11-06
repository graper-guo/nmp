<?php

namespace Daily;

use Nos\Base\BaseModel;

class DailyModel extends BaseModel
{

    protected static $table = 'nmp_daily';

    /**
     * 增加日报
     * @param $params
     * @return bool|int
     * @throws \Nos\Exception\CoreException
     */
    public function insertDaily($params)
    {
        if (empty($params)) {
            return false;
        }

        $res = self::insert($params);
        if (empty($res)) {
            throw new CoreException("内容插入失败");
        }

        return $res;
    }

    /**
     * 更新日报内容
     * @param $params
     * @return bool|int
     * @throws \Nos\Exception\CoreException
     */
    public function updateDaily($params, $where)
    {
        if (empty($params) || empty($where)) {
            return false;
        }

        $res = self::update($params, $where);
        if (empty($res)) {
            throw new CoreException("内容更新失败");
        }

        return $res;
    }

    /**
     * 获取日报内容
     * @param $params
     * @param $where
     * @param array $option
     * @return array|bool
     * @throws \Nos\Exception\CoreException
     */
    public function getDaily($where, $option = [])
    {
        if (empty($where)) {
            return false;
        }

        $res = self::select([], $where, $option);
        if ($res === false) {
            throw new CoreException("内容获取失败");
        }

        return $res;
    }



}