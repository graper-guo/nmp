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

class Login_LogoutController extends BaseController
{
    protected $schema = [
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
            throw new CoreException("token cant't be empty");
        }
        $data = $loginModel->logout($token);
        Response::apiResponse($data['status'], $data['msg'], $data['data']);
    }

}