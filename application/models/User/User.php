<?php

namespace User;
use Nos\Http\ApiClient;
use Nos\Http\RpcClient;

class UserModel
{
    /**
     * 根据token获取用户信息
     * @param $token
     * @return bool|string
     * @throws \Nos\Exception\CoreException
     */
    public function getUserInfo($token)
    {
        $apiClient = new RpcClient();
        $apiClient = $apiClient->getInstance('UserService');

        $actionName = '/unified/user/get';
        $params = [
            'unified_token' => $token,
            'timestamp' => time()
        ];
        $res = $apiClient->send(ApiClient::REQUEST_TYPE_POST, $actionName, $params);
        return $res;
    }

    /**
     * 根据token修改用户信息
     * @param $token
     * @param $data
     * @return bool|string
     * @throws \Nos\Exception\CoreException
     */
    public function updateUserInfo($token, $data)
    {
        $apiClient = new RpcClient();
        $apiClient = $apiClient->getInstance('UserService');

        $actionName = '/unified/user/update';
        $params = [
            'unified_token' => $token,
            'data'  => $data,
            'timestamp' => time()
        ];
        $res = $apiClient->send(ApiClient::REQUEST_TYPE_POST, $actionName, $params);
        return $res;
    }
}