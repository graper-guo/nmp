<?php

namespace Login;
use Nos\Comm\Config;
use Nos\Http\ApiClient;
use Nos\Http\RpcClient;

class LoginModel
{
    /**
     * 注册接口
     * @param $accout
     * @param $password
     * @return bool|string
     * @throws \Nos\Exception\CoreException
     */
    public function registered($accout ,$password)
    {
        $moduleName = 'UserService';
        $apiClient = new RpcClient();
        $apiClient = $apiClient->getInstance($moduleName);

        $httpIni = Config::get($moduleName);
        $actionName = '/unified/register';
        $params = [
            'email' => $accout,
            'password' => $password,
            'timestamp' => time(),
            'callback_url' => $httpIni['callback']
        ];
        $res = $apiClient->send(ApiClient::REQUEST_TYPE_POST, $actionName, $params);
        return $res;
    }

    /**
     * 有token登录
     * @param $token
     * @return bool|string
     * @throws \Nos\Exception\CoreException
     */
    public function loginWithToken($token)
    {
        $apiClient = new RpcClient();
        $apiClient = $apiClient->getInstance('UserService');

        $actionName = '/unified/login';
        $params = [
            'unified_token' => $token,
            'timestamp' => time()
        ];
        $res = $apiClient->send(ApiClient::REQUEST_TYPE_POST, $actionName, $params);
        return $res;
    }

    /**
     * 无token登录
     * @param $email
     * @param $password
     * @return bool|string
     * @throws \Nos\Exception\CoreException
     */
    public function loginNotoken($email, $password)
    {
        $apiClient = new RpcClient();
        $apiClient = $apiClient->getInstance('UserService');

        $acitionName = '/unified/login';
        $params = [
            'email' => $email,
            'password' => $password,
        ];
        $res = $apiClient->send(ApiClient::REQUEST_TYPE_POST, $acitionName, $params);
        return $res;
    }

    /**
     * 登出
     * @param $token
     * @return bool|string
     * @throws \Nos\Exception\CoreException
     */
    public function logout($token)
    {
        $apiClient = new RpcClient();
        $apiClient = $apiClient->getInstance('UserService');

        $acitionName = '/unified/logout';
        $params = [
            'unified_token' => $token,
            'timestamp' => time()
        ];
        $res = $apiClient->send(ApiClient::REQUEST_TYPE_POST, $acitionName, $params);
        return $res;
    }


}