<?php

use Common\App\AppModel;
use Nos\Comm\Validator;
use Nos\Exception\CoreException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Exception\ResourceNotFoundException;
use Nos\Exception\UnauthorizedException;
use Nos\Http\Request;
use Yaf\Controller_Abstract;

abstract class BaseController extends Controller_Abstract
{

    /**
     * 是否需要接口鉴权
     * @var bool $auth
     */
    protected $auth = false;
    protected $params;

    protected $schema = [];


    /**
     * 接口鉴权函数
     * @return bool
     * @throws CoreException
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     * @throws UnauthorizedException
     */
    protected function auth()
    {
        Validator::make($params = Request::all(), [
            'appId' => 'required',
            'accessToken' => 'required',
            'timestamp' => 'required'
        ]);
        $appId = $params['appId'];
        $appSecret = AppModel::get($appId);
        // 与客户端采用同样的加密算法
        $backAccessToken = md5($params['timestamp'] . $appId .  $appSecret);
        $frontAccessToken = $params['accessToken'];
        // 判断前后端的accessToken是否相等
        if ($frontAccessToken != $backAccessToken) {
            throw new UnauthorizedException("auth|app:{$appId}_auth_failed
            |frontAccessToken:{$params['accessToken']}
            |backAccessToken:{$backAccessToken}
            |timestamp:{$params['timestamp']}");
        }
        return true;
    }

    /**
     * 业务逻辑
     */
    abstract protected function indexAction();

    /**
     * 初始化
     * @param array $params
     * @throws CoreException
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     * @throws UnauthorizedException
     */
    private function init()
    {
        $this->auth && $this->auth();
        $request_params = array_merge($_GET, $_POST);
        $this->params = $request_params;
        if (!empty($this->schema)) {
            $this->params = Paramcheck::check($this->params, $this->schema);
        }
    }

}