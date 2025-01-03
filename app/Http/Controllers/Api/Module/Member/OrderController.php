<?php
namespace App\Http\Controllers\Api\Module\Member;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

use App\Http\Constant\Code;
use App\TraitClass\ToolTrait;
use App\Http\Constant\RedisKey;
use App\Models\Api\Module\Printer;
use App\Events\Api\Member\PayEvent;
use App\Events\Api\Member\TotalEvent;
use App\Events\Api\Member\OrderEvent;
use App\Http\Controllers\Api\BaseController;


/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-07-01
 *
 * 会员订单控制器类
 */
class OrderController extends BaseController
{
  use ToolTrait;

  // 模型名称
  protected $_model = 'App\Models\Api\Module\Order';

  // 关联对像
  protected $_relevance = [
    'list' => false,
    'view' => [
      'manager',
      'printer',
      'price'
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
   * @api {post} /api/member/order/first_step 03. 打印第一步
   * @apiDescription 当前会员打印第一步: 上传文件
   * @apiGroup 23. 会员订单模块
   * @apiPermission jwt
   * @apiHeader {String} Authorization 身份令牌
   * @apiHeaderExample {json} Header-Example:
   * {
   *   "Authorization": "Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiO"
   * }
   *
   * @apiParam {String} token 二维码密文
   * @apiParam {String} filename 打印文件名称
   * @apiParam {String} url 打印原始文件地址
   * @apiParam {String} pdf_url 打印PDF文件地址
   *
   * @apiSampleRequest /api/member/order/first_step
   * @apiVersion 1.0.0
   */
  public function first_step(Request $request)
  {
    $messages = [
      'token.required' => '请您输入一级代理商自增编号',
      'filename.required' => '请您输入打印文件名称',
      'url.required' => '请您上传打印文件',
      'pdf_url.required' => '请您上传打印文件',
    ];

    $rule = [
      'token' => 'required',
      'filename' => 'required',
      'url' => 'required',
      'pdf_url' => 'required',
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
        // 将内容解析成数组
        $data = explode(';', $request->token);

        // 如果无法解析返回错误
        if(!is_array($data))
        {
          return self::error(Code::ERROR);
        }

        $first_level_agent_id = self::decrypt($data['0']);
        $second_level_agent_id = self::decrypt($data['1']);
        $manager_id = self::decrypt($data['2']);
        $printer_id = self::decrypt($data['3']);

        $printer = Printer::getRow(['id' => $printer_id, 'status' => 1]);

        // 打印机不可用
        if(empty($printer->id))
        {
          return self::error(Code::PRINTER_ERROR);
        }

        $model = $this->_model::firstOrNew(['id' => $request->id]);

        if(empty($request->id))
        {
          $model->order_no = self::getOrderNo();
        }

        $model->organization_id = self::getOrganizationId();
        $model->first_level_agent_id = $first_level_agent_id;
        $model->second_level_agent_id = $second_level_agent_id;
        $model->manager_id = $manager_id;
        $model->member_id = self::getCurrentId();
        $model->printer_id = $printer_id;
        $model->title = $request->filename;
        $model->save();

        $data = [
          'url' => $request->url,
          'pdf_url' => $request->pdf_url
        ];

        $model->resource()->delete();
        $model->resource()->create($data);

        // 计算打印文件页数
        event(new TotalEvent($model, $request->pdf_url));

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
   * @api {post} /api/member/order/second_step 04. 打印第二步
   * @apiDescription 当前会员打印第二步: 确认打印份数
   * @apiGroup 23. 会员订单模块
   * @apiPermission jwt
   * @apiHeader {String} Authorization 身份令牌
   * @apiHeaderExample {json} Header-Example:
   * {
   *   "Authorization": "Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiO"
   * }
   *
   * @apiParam {Array} order_id 订单自增编号
   * @apiParam {String} print_total 打印份数
   *
   * @apiSampleRequest /api/member/order/second_step
   * @apiVersion 1.0.0
   */
  public function second_step(Request $request)
  {
    $messages = [
      'order_id.required' => '请您输入订单自增编号',
      'print_total.required' => '请您输入打印份数',
    ];

    $rule = [
      'order_id' => 'required',
      'print_total' => 'required',
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
        $model = $this->_model::getRow(['id' => $request->order_id]);

        $model->print_total = $request->print_total;
        $model->save();

        // 订单处理
        event(new OrderEvent($model));

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
   * @api {post} /api/member/order/pay 05. 订单支付[TODO]
   * @apiDescription 当前会员订单支付
   * @apiGroup 23. 会员订单模块
   * @apiPermission jwt
   * @apiHeader {String} Authorization 身份令牌
   * @apiHeaderExample {json} Header-Example:
   * {
   *   "Authorization": "Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiO"
   * }
   *
   * @apiParam {string} order_id 订单自增编号
   *
   * @apiSampleRequest /api/member/order/pay
   * @apiVersion 1.0.0
   */
  public function pay(Request $request)
  {
    $messages = [
      'order_id.required' => '请您输入订单自增编号',
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

        // 支付
        $result = event(new PayEvent($model));

        if(empty($result[0]))
        {
          return self::error(Code::PAY_ERROR);
        }

        $response = $result[0];

        DB::commit();

        return self::success($response);
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
   * @api {post} /api/member/order/again 06. 二次打印
   * @apiDescription 当前会员重新打印
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
   * @apiSampleRequest /api/member/order/again
   * @apiVersion 1.0.0
   */
  public function again(Request $request)
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
        // 打印队列Socket消耗
        $key = RedisKey::SOCKET_PRINT_QUEUE;

        // 将订单自增编号插入打印队列
        Redis::rpush($key, $request->id);

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


  /**
   * @api {post} /api/member/order/delete 07. 删除记录
   * @apiDescription 当前会员订单记录
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
