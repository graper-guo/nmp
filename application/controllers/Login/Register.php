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
use Nos\Http\Request;
use Nos\Http\Response;
use Nos\Comm\Validator;
use Login\LoginModel;

class Login_RegisterController extends BaseController
{
    protected $schema = [
        'email'         => ['type' => 'string', 'errorMsg' => 'email不能为空'],
        'password'      => ['type' => 'string', 'defaultV' => 'password不能为空'],
    ];

    /**
     * 业务逻辑
     * @throws CoreException
     * @throws ParamValidateFailedException
     */
    public function indexAction()
    {
        $loginModel = new LoginModel();
        $data = $loginModel->registered($this->params['email'], $this->params['password']);
        Response::apiResponse($data['status'], $data['msg'], $data['data']);
    }
}