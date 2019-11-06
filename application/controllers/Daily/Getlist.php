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
use Daily\DailyModel;

class Daily_GetlistController extends BaseController
{
    protected $schema = [
        'date'  => ['type' => 'string', 'errorMsg' => '请输入记录日期!'],
        'type'  => ['type' => 'number', 'errorMsg' => '请输入类型!'],
    ];

    /**
     * 业务逻辑
     * @throws CoreException
     * @throws ParamValidateFailedException
     */
    public function indexAction()
    {
        $where = [
            ['date', '=', $this->params['date']],
            ['type', '=', $this->params['type']]
        ];
        $option = [
            'order' => ['id' => 'desc']
        ];
        $userModel = new DailyModel();
        $data = $userModel->getDaily($where, $option);
        Response::apiSuccess($data);
    }
}