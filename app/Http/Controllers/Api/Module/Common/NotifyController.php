<?php
namespace App\Http\Controllers\Api\Module\Common;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

use Yansongda\Pay\Log;
use Yansongda\Pay\Pay;
use App\Http\Constant\Code;
use App\Http\Constant\RedisKey;
use App\Models\Common\Module\Order;
use App\Http\Controllers\Api\BaseController;
use Yansongda\Pay\Exceptions\GatewayException;


/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2022-01-01
 *
 * 回调控制器类
 */
class NotifyController extends BaseController
{

  /**
   * @api {post} /api/common/notify/wechat 14. 微信支付回调
   * @apiDescription 获取微信支付回调
   * @apiGroup 02. 公共模块
   *
   * @apiSampleRequest /api/common/notify/wechat
   * @apiVersion 1.0.0
   */
  public function wechat(Request $request)
  {
    DB::beginTransaction();

    try
    {
      Log::info('微信支付回调开始 <================ 支付中');

      $config = config('pay.wechat');

      $pay = Pay::wechat($config);

      $data = $pay->verify(); // 验签

      Log::debug('微信回调参数', $data->all());

      $order_no = $data->out_trade_no;

      Log::info('订单编号====' . $order_no);

      $where = [
        'order_no' => $order_no,
        'status'   => 1
      ];

      $model = Order::getRow($where);

      if(empty($model->id))
      {
        Log::info('订单未找到');

        return false;
      }

      $model->confirm_status = 1;
      $model->save();

      Log::info('支付成功');

      // 打印队列Socket消耗
      $key = RedisKey::SOCKET_PRINT_QUEUE;

      // $redis = Redis::connection('session');

      // 将订单自增编号插入打印队列
      Redis::rpush($key, $model->id);

      DB::commit();

      return $pay->success()->send();
    }
    catch(\Exception $e)
    {
      DB::rollback();

      $content = '在文件 ' . $e->getFile();
      $content .= ' 的 ' . $e->getLine();
      $content .= ' 行 ' .$e->getMessage();

      Log::info('支付失败====' . $content);
    }
  }


  /**
   * @api {post} /api/common/notify/test 14. 测试[TODO]
   * @apiDescription 获取微信支付回调
   * @apiGroup 02. 公共模块
   *
   * @apiSampleRequest /api/common/notify/test
   * @apiVersion 1.0.0
   */
  public function test(Request $request)
  {
    try
    {
      // 打印队列Socket消耗
      $key = RedisKey::SOCKET_PRINT_QUEUE;

      // 将订单自增编号插入打印队列
      Redis::rpush($key, $request->order_id);
    }
    catch(\Exception $e)
    {
      record($e);
    }
  }


  /**
   * @api {post} /api/common/notify/test2 14. 测试[TODO]
   * @apiDescription 获取微信支付回调
   * @apiGroup 02. 公共模块
   *
   * @apiSampleRequest /api/common/notify/test2
   * @apiVersion 1.0.0
   */
  public function test2(Request $request)
  {
    try
    {
      // 打印队列Socket消耗
      $key = RedisKey::SOCKET_PRINT_QUEUE;

      // 将订单自增编号插入打印队列
      $response = Redis::llen($key);

      return self::success($response);
    }
    catch(\Exception $e)
    {
      record($e);
    }
  }
}
