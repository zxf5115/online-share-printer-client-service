<?php
namespace App\Listeners\Api\Member;

use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\TraitClass\ToolTrait;
use App\Models\Common\System\Config;
use App\Events\Api\Member\DivideEvent;
use App\Models\Api\Module\Order\Resource;
use App\Models\Api\Module\Organization\Asset;
use App\Models\Api\Module\Organization\Obtain;

/**
 * 订单分红监听器
 */
class DivideListeners
{
  use ToolTrait;

  /**
   * Create the event listener.
   *
   * @return void
   */
  public function __construct()
  {
      //
  }

  /**
   * Handle the event.
   *
   * @param  DivideEvent  $event
   * @return void
   */
  public function handle(DivideEvent $event)
  {
    try
    {
      // 订单信息
      $order = $event->order;

      if(empty($order->id))
      {
        Log::error('订单异常(订单数据为空)');

        return false;
      }

      // 文件总数
      $total = bcmul($order->page_total, $order->print_total);

      // 直属店长收益
      $manager_proportion = 0.00;

      // 一级代理商
      $first_level_agent_id = $order->first_level_agent_id;

      // 二级代理商
      $second_level_agent_id = $order->second_level_agent_id;

      // 店长
      $manager_id = $order->manager_id;

      // 如果一级代理商不存在
      if(empty($first_level_agent_id))
      {
        return false;
      }

      // 如果店长不存在
      if(empty($manager_id))
      {
        return false;
      }

      // 平台手续费税率
      $rate = 0; ///Config::getValue('value', ['title' => 'withdrawal_rate']);

      // 一级代理商收益
      $manager_proportion = $this->agent($order->id, $first_level_agent_id, $total, $rate);

      // 如果二级代理商存在
      if(!empty($second_level_agent_id))
      {
        // 二级代理商收益
        $manager_proportion = $this->agent($order->id, $second_level_agent_id, $total, $rate);
      }

      // 店长收益
      $this->manager($order->id, $manager_id, $total, $manager_proportion, $rate);

      return true;
    }
    catch(\Exception $e)
    {
      record($e);

      return false;
    }
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2022-02-26
   * ------------------------------------------
   * 代理商收益
   * ------------------------------------------
   *
   * 代理商收益
   *
   * @param [type] $order_id 订单编号
   * @param [type] $member_id 代理商编号
   * @param [type] $total 打印总页数
   * @return [type]
   */
  private function agent($order_id, $member_id, $total, $rate)
  {
    // 查询一级代理商资产信息
    $asset = Asset::getRow(['member_id' => $member_id]);

    if(empty($asset->id))
    {
      return false;
    }

    // 直属店长收益
    $response = $asset->manger_proportion;

    // 一级代理商收益
    $first_level_agent_proportion = $asset->proportion;

    // 收益金额
    $money = bcmul($first_level_agent_proportion, $total, 2);

    if($rate > 0)
    {
      $withdrawal_rate = bcdiv($rate, 100, 2);

      $money = bcsub($money, bcmul($money, $withdrawal_rate, 2), 2);
    }

    // 收益记录
    $model = new Obtain();
    $model->member_id = $member_id;
    $model->order_id = $order_id;
    $model->money = $money;
    $model->save();

    $asset->increment('money', $money);
    $asset->save();

    return $response;
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2022-02-26
   * ------------------------------------------
   * 代理商收益
   * ------------------------------------------
   *
   * 代理商收益
   *
   * @param [type] $order_id 订单编号
   * @param [type] $member_id 代理商编号
   * @param [type] $total 打印总页数
   * @param [type] $proportion 单页收益金额
   * @return [type]
   */
  private function manager($order_id, $member_id, $total, $proportion, $rate)
  {
    // 查询一级代理商资产信息
    $asset = Asset::getRow(['member_id' => $member_id]);

    if(empty($asset->id))
    {
      return false;
    }

    // 收益金额
    $money = bcmul($proportion, $total, 2);

    if($rate > 0)
    {
      $withdrawal_rate = bcdiv($rate, 100, 2);

      $money = bcsub($money, bcmul($money, $withdrawal_rate, 2), 2);
    }

    // 收益记录
    $model = new Obtain();
    $model->member_id = $member_id;
    $model->order_id = $order_id;
    $model->money = $money;
    $model->save();

    $asset->increment('money', $money);
    $asset->save();
  }
}
