<?php
/**
 * 控制器示例
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 15:37
 */

use Nos\Exception\CoreException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Http\Response;
use Login\LoginModel;

class Login_LoginController extends BaseController
{
    protected $schema = [
        'email'         => ['type' => 'string', 'defaultV' => ''],
        'password'      => ['type' => 'string', 'defaultV' => ''],
    ];

    /**
     * 业务逻辑
     * @throws CoreException
     * @throws ParamValidateFailedException
     */
    public function indexAction()
    {
        $token = $_COOKIE['unified_token'] ?? '';
        $loginModel = new LoginModel();
        if (empty($token)) {
            $data = $loginModel->loginNotoken($this->params['email'], $this->params['password']);
            Response::apiResponse($data['status'], $data['msg'], $data['data']);
        }

        $data = $loginModel->loginWithToken($token);
        Response::apiResponse($data['status'], $data['msg'], $data['data']);
    }
}