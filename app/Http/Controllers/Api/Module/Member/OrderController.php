<?php
namespace App\Http\Controllers\Api\Module\Member;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Constant\Code;
use App\Events\Api\Member\AssetEvent;
use App\Events\Api\Member\MoneyEvent;
use App\Events\Common\Push\AuroraEvent;
use App\Models\Common\Module\Member\Asset;
use App\Events\Api\Member\CoursewareEvent;
use App\Http\Controllers\Api\BaseController;
use App\Models\Api\Module\Education\Courseware;
use App\Models\Api\Module\Member\Courseware as MemberCourseware;


/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-07-01
 *
 * 会员订单控制器类
 */
class OrderController extends BaseController
{
  // 模型名称
  protected $_model = 'App\Models\Api\Module\Order';

  // 关联对像
  protected $_relevance = [
    'list' => false,
    'view' => [
      'manager',
      'printer',
    ]
  ];


  /**
   * @api {get} /api/member/order/list?page={page} 01. 我的订单列表
   * @apiDescription 获取当前会员订单列表(分页)
   * @apiGroup 23. 会员订单模块
   * @apiPermission jwt
   * @apiHeader {String} Authorization 身份令牌
   * @apiHeaderExample {json} Header-Example:
   * {
   *   "Authorization": "Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiO"
   * }
   *
   * @apiParam {int} page 当前页数
   *
   * @apiSuccess (字段说明|订单) {String} id 订单编号
   * @apiSuccess (字段说明|订单) {String} order_no 订单号
   * @apiSuccess (字段说明|订单) {String} first_level_agent_id 一级代理商自增编号
   * @apiSuccess (字段说明|订单) {String} second_level_agent_id 二级代理商自增编号
   * @apiSuccess (字段说明|订单) {String} manager_id 店长自增编号
   * @apiSuccess (字段说明|订单) {String} printer_id 打印机自增编号
   * @apiSuccess (字段说明|订单) {String} member_id 会员编号
   * @apiSuccess (字段说明|订单) {String} type 打印类型
   * @apiSuccess (字段说明|订单) {String} title 打印文件名称
   * @apiSuccess (字段说明|订单) {String} page_total 文件页数
   * @apiSuccess (字段说明|订单) {String} print_total 打印份数
   * @apiSuccess (字段说明|订单) {String} pay_money 支付金额
   * @apiSuccess (字段说明|订单) {String} pay_type 支付类型
   * @apiSuccess (字段说明|订单) {String} pay_status 支付状态
   * @apiSuccess (字段说明|订单) {String} pay_time 支付时间
   * @apiSuccess (字段说明|订单) {String} order_status 订单状态
   * @apiSuccess (字段说明|订单) {String} create_time 创建时间
   *
   * @apiSampleRequest /api/member/order/list
   * @apiVersion 1.0.0
   */
  public function list(Request $request)
  {
    try
    {
      $condition = self::getCurrentWhereData();

      // 对用户请求进行过滤
      $filter = $this->filter($request->all());

      $condition = array_merge($condition, $this->_where, $filter);

      // 获取关联对象
      $relevance = self::getRelevanceData($this->_relevance, 'list');

      $response = $this->_model::getPaging($condition, $relevance, $this->_order);

      return self::success($response);
    }
    catch(\Exception $e)
    {
      // 记录异常信息
      self::record($e);

      return self::error(Code::ERROR);
    }
  }


  /**
   * @api {get} /api/member/order/view/{id} 02. 我的订单详情
   * @apiDescription 获取当前会员订单的详情
   * @apiGroup 23. 会员订单模块
   * @apiPermission jwt
   * @apiHeader {String} Authorization 身份令牌
   * @apiHeaderExample {json} Header-Example:
   * {
   *   "Authorization": "Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiO"
   * }
   *
   * @apiParam {int} id 订单自增编号
   *
   * @apiSuccess (字段说明|订单) {String} id 订单编号
   * @apiSuccess (字段说明|订单) {String} order_no 订单号
   * @apiSuccess (字段说明|订单) {String} first_level_agent_id 一级代理商自增编号
   * @apiSuccess (字段说明|订单) {String} second_level_agent_id 二级代理商自增编号
   * @apiSuccess (字段说明|订单) {String} manager_id 店长自增编号
   * @apiSuccess (字段说明|订单) {String} printer_id 打印机自增编号
   * @apiSuccess (字段说明|订单) {String} member_id 会员编号
   * @apiSuccess (字段说明|订单) {String} type 打印类型
   * @apiSuccess (字段说明|订单) {String} title 打印文件名称
   * @apiSuccess (字段说明|订单) {String} page_total 文件页数
   * @apiSuccess (字段说明|订单) {String} print_total 打印份数
   * @apiSuccess (字段说明|订单) {String} pay_money 支付金额
   * @apiSuccess (字段说明|订单) {String} pay_type 支付类型
   * @apiSuccess (字段说明|订单) {String} pay_status 支付状态
   * @apiSuccess (字段说明|订单) {String} pay_time 支付时间
   * @apiSuccess (字段说明|订单) {String} order_status 订单状态
   * @apiSuccess (字段说明|订单) {String} create_time 创建时间
   * @apiSuccess (字段说明|店长) {String} id 店长自增编号
   * @apiSuccess (字段说明|店长) {String} nickanme 店长姓名
   * @apiSuccess (字段说明|打印机) {String} id 打印机自增编号
   * @apiSuccess (字段说明|打印机) {String} code 打印机编号
   * @apiSuccess (字段说明|打印机) {String} model 打印机型号
   * @apiSuccess (字段说明|打印机) {String} address 打印机地址
   *
   * @apiSampleRequest /api/member/order/view/{id}
   * @apiVersion 1.0.0
   */
  public function view(Request $request, $id)
  {
    try
    {
      $condition = self::getCurrentWhereData();

      $where = ['id' => $id];

      $condition = array_merge($condition, $where);

      // 获取关联对象
      $relevance = self::getRelevanceData($this->_relevance, 'view');

      $response = $this->_model::getRow($condition, $relevance);

      return self::success($response);
    }
    catch(\Exception $e)
    {
      // 记录异常信息
      self::record($e);

      return self::error(Code::ERROR);
    }
  }


  /**
   * @api {post} /api/member/order/handle 03. 创建订单
   * @apiDescription 当前会员创建订单
   * @apiGroup 23. 会员订单模块
   * @apiPermission jwt
   * @apiHeader {String} Authorization 身份令牌
   * @apiHeaderExample {json} Header-Example:
   * {
   *   "Authorization": "Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiO"
   * }
   *
   * @apiParam {Array} printer_id 打印机自增编号
   * @apiParam {String} pay_money 支付金额
   * @apiParam {String} pay_type 支付类型 1 支付宝 2 微信
   *
   * @apiSampleRequest /api/member/order/handle
   * @apiVersion 1.0.0
   */
  public function handle(Request $request)
  {
    $messages = [
      'printer_id.required' => '请您输入课程编号',
      'pay_money.required'  => '请您输入支付金额',
      'pay_type.required'   => '请您选择支付类型',
    ];

    $rule = [
      'printer_id' => 'required',
      'pay_money'  => 'required',
      'pay_type'   => 'required',
    ];

    // 验证用户数据内容是否正确
    $validation = self::validation($request, $messages, $rule);

    if(!$validation['status'])
    {
      return $validation['message'];
    }
    else
    {
      DB::beginTransaction();

      try
      {
        // 判断课程是否存在
        foreach($request->courseware_id as $courseware_id)
        {
          $courseware = Courseware::getRow(['id' => $courseware_id]);

          if(empty($courseware))
          {
            return self::error(Code::COURSE_EMPTY);
          }

          $where = [
            'member_id'     => self::getCurrentId(),
            'courseware_id' => $courseware_id,
          ];

          $memberCourseware = MemberCourseware::getRow($where);

          // 一门课程只能购买一次
          if(!empty($memberCourseware->id))
          {
            return self::error(Code::COURSE_EXITS);
          }
        }

        $model = $this->_model::firstOrNew(['id' => $request->id]);

        if(empty($request->id))
        {
          $rand = str_pad(rand(1, 999999), 6, 0, STR_PAD_LEFT);

          $model->order_no = date('YmdHis') . $rand;
        }

        $model->organization_id = self::getOrganizationId();
        $model->member_id       = self::getCurrentId();
        $model->pay_money       = $request->pay_money;
        $model->pay_type        = $request->pay_type;
        $model->save();

        $data = self::packRelevanceData($request, 'courseware_id');

        foreach($data as &$item)
        {
          $item['member_id'] = self::getCurrentId();
        }

        if(!empty($data))
        {
          $model->coursewareRelevance()->delete();
          $model->coursewareRelevance()->createMany($data);
        }

        DB::commit();

        return self::success($model);
      }
      catch(\Exception $e)
      {
        DB::rollback();

        // 记录异常信息
        self::record($e);

        return self::error(Code::HANDLE_FAILURE);
      }
    }
  }


  /**
   * @api {post} /api/member/order/pay 04. 订单支付
   * @apiDescription 当前会员订单支付
   * @apiGroup 23. 会员订单模块
   * @apiPermission jwt
   * @apiHeader {String} Authorization 身份令牌
   * @apiHeaderExample {json} Header-Example:
   * {
   *   "Authorization": "Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiO"
   * }
   *
   * @apiParam {string} order_id 订单编号
   *
   * @apiSampleRequest /api/member/order/pay
   * @apiVersion 1.0.0
   */
  public function pay(Request $request)
  {
    $messages = [
      'order_id.required' => '请您输入订单编号',
    ];

    $rule = [
      'order_id' => 'required',
    ];

    // 验证用户数据内容是否正确
    $validation = self::validation($request, $messages, $rule);

    if(!$validation['status'])
    {
      return $validation['message'];
    }
    else
    {
      DB::beginTransaction();

      try
      {
        $member_id = self::getCurrentId();

        $condition = self::getCurrentWhereData();

        $where = ['id' => $request->order_id];

        $condition = array_merge($condition, $where);

        $model = $this->_model::getRow($condition);

        // 如果订单数据为空或者用户信息为空
        if(empty($model) || empty($member_id))
        {
          return self::error(Code::DATA_ERROR);
        }

        $model->pay_status = 1;
        $model->pay_time   = time();
        $model->save();

        // 获取当前用户资产
        $asset = Asset::getRow(['member_id' => $member_id]);

        // 如果当前用户没有资产信息
        if(empty($asset))
        {
          return self::error(Code::CURRENT_MEMBER_ASSET_EMPTY);
        }

        // 如果订单金额大于当前用户资产总额
        if($model->pay_money > $asset->money)
        {
          return self::error(Code::CURRENT_MEMBER_ASSET_DEFICIENCY);
        }

        $courseware = $model->coursewareRelevance()->pluck('courseware_id')->toArray();

        if(empty($courseware))
        {
          return self::error(Code::CURRENT_ORDER_COURSE_EXITS);
        }

        // 添加课程
        $result = event(new CoursewareEvent($member_id, $courseware));

        if(empty($result[0]))
        {
          return self::error(Code::COURSEWARE_ADD_ERROR);
        }

        // 扣除费用
        $result = event(new AssetEvent($member_id, $model->pay_money, 2));

        if(empty($result[0]))
        {
          return self::error(Code::PAY_ERROR);
        }

        // 增加资产消费记录
        event(new MoneyEvent($member_id, $model->pay_money, 2));

        $data = [
          'title'     => '订单消息',
          'content'   => '您的订单已支付',
        ];

        // 消息推送
        event(new AuroraEvent(1, $data, $model->member_id));

        DB::commit();

        return self::success(Code::message(Code::PAY_SUCCESS));
      }
      catch(\Exception $e)
      {
        DB::rollback();

        // 记录异常信息
        self::record($e);

        return self::error(Code::HANDLE_FAILURE);
      }
    }
  }


  /**
   * @api {post} /api/member/order/delete 05. 删除记录
   * @apiDescription 当前会员把课程删除购物车
   * @apiGroup 23. 会员订单模块
   * @apiPermission jwt
   * @apiHeader {String} Authorization 身份令牌
   * @apiHeaderExample {json} Header-Example:
   * {
   *   "Authorization": "Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiO"
   * }
   *
   * @apiParam {string} id 订单自增编号
   *
   * @apiSampleRequest /api/member/order/delete
   * @apiVersion 1.0.0
   */
  public function delete(Request $request)
  {
    $messages = [
      'id.required' => '请您输入订单自增编号',
    ];

    $rule = [
      'id' => 'required',
    ];

    // 验证用户数据内容是否正确
    $validation = self::validation($request, $messages, $rule);

    if(!$validation['status'])
    {
      return $validation['message'];
    }
    else
    {
      try
      {
        $this->_model::destroy($request->id);

        return self::success(Code::message(Code::HANDLE_SUCCESS));
      }
      catch(\Exception $e)
      {
        // 记录异常信息
        self::record($e);

        return self::error(Code::HANDLE_FAILURE);
      }
    }
  }
}
