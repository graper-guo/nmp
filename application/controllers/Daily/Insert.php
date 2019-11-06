<?php
/**
 * 控制器示例
 * Created by PhpStorm.
 * User: grape
 * Date: 2018-11-28
 * Time: 15:37
 */

use Nos\Exception\CoreException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Http\Response;
use User\UserModel;
use Daily\DailyModel;

class Daily_InsertController extends BaseController
{
    protected $schema = [
        'title' => ['type' => 'string', 'errorMsg' => '请输入文章标题!'],
        'date'  => ['type' => 'string', 'errorMsg' => '请输入记录日期!'],
        'type'  => ['type' => 'number', 'errorMsg' => '请输入类型!'],
        'info'  => ['type' => 'string', 'defautV' => []],
    ];

    /**
     * 业务逻辑
     * @throws CoreException
     * @throws ParamValidateFailedException
     */
    public function indexAction()
    {
        $token = $_COOKIE['unified_token'] ?? '';
        if (empty($token)) {
            throw new CoreException("token cant't be empty");
        }
        Checkparams::filterParams($this->params, $this->schema);
        $userInfo = $this->getUserInfo($token);

        $params = [
            'title' => $this->params['title'],
            'date'  => $this->params['date'],
            'type'  => $this->params['type'],
            'info'  => $this->params['info'] ?? '',
            'op_id' => $userInfo['id'] ?? 0,
            'op_name' => $userInfo['name'] ?? ''
        ];
        $userModel = new DailyModel();

        $data = $userModel->insertDaily($params);

        Response::apiResponse($data['status'], $data['msg'], $data['data']);
    }

    public function getUserInfo($token)
    {
        $userModel = new UserModel();
        if (empty($token)) {
            throw new CoreException("token cant't be empty");
        }
        $data = $userModel->getUserInfo($token);
        if (empty($data)) {
            throw new CoreException("用户信息为空");
        }
        return $data;
    }

}