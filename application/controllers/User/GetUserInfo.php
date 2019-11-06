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
class User_GetUserInfoController extends BaseController
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
        $userModel = new UserModel();
        if (empty($token)) {
            throw new CoreException("token cant't be empty");
        }
        $data = $userModel->getUserInfo($token);

        Response::apiResponse($data['status'], $data['msg'], $data['data']);
    }

}