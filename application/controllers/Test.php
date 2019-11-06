<?php

use Nos\Http\Response;
use Nos\Comm\Apiclient;

class TestController extends BaseController
{
    /**
     * 业务逻辑
     */
    protected function indexAction()
    {
       $api = new Apiclient("UserService");

       $data = [
           'appId' => 'uc_all',
           'accessToken' => 111,
           'timestamp'   => 111,
           'email' => "1231221333@qq.com",
           'password' => "123www"
       ];

        $connomains = [
            [
            'path'   => '/unified/register',
            'params' => $data
                ]
        ];
//       $res = $api->curlApi("/unified/register", $data, 1);
//        $res = $api->curlApiMulti($connomains, 0);
        $res = $api->curlApiMulti($connomains, 1);
        var_dump($res);die;
        Response::apiResponse($res['status'], $res['msg'], $res['data']);
    }
}