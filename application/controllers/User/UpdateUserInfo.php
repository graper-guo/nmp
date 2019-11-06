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
use User\UserModel;

class User_UpdateUserInfoController extends BaseController
{
    protected $schema = [
        'password' => ['type' => 'string', 'defautV' => []],
    ];

    /**
     * 业务逻辑
     * @throws CoreException
     * @throws ParamValidateFailedException
     */
    public function indexAction()
    {
        Checkparams::filterParams($this->params, $this->schema);
        $token = $_COOKIE['unified_token'] ?? '';

        if (empty($token)) {
            throw new CoreException("token cant't be empty");
        }
        $data = [
            'password' => $this->params['password']
        ];

        $userModel = new UserModel();
        $data = $userModel->updateUserInfo($token, $data);

        Response::apiResponse($data['status'], $data['msg'], $data['data']);
    }


}