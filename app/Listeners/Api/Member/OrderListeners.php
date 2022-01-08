<?php
namespace App\Listeners\Api\Member;

use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\TraitClass\ToolTrait;
use App\Models\Api\Module\Price;
use App\Events\Api\Member\OrderEvent;

/**
 * 订单监听器
 */
class OrderListeners
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
   * @param  OrderEvent  $event
   * @return void
   */
  public function handle(OrderEvent $event)
  {
    try
    {
      $response = null;

      // 订单信息
      $order = $event->order;

      if(empty($order))
      {
        Log::error('订单异常(订单数据为空)');

        return false;
      }

      // 打印文件页数
      $total = $order->page_total;

      // 微信支付
      if(!empty($total))
      {
        Log::error('数据异常(打印文件页数不为空)');

        return true;
      }

      $resource = Resource::getRow(['order_id' => $order->id]);

      if(empty($resource->id))
      {
        Log::error('订单资源文件异常(订单资源文件数据为空)');

        return false;
      }

      $url = $resource->pdf_url;

      $page_total = 0;

      if(file_exists($url))
      {
        // 计算PDF页数
        $page_total = self::getPageTotal($url);

        $price = Price::getRow(['id' => $order->type]);

        if(empty($price->id))
        {
          Log::error('订单资源文件异常(订单资源文件数据为空)');

          return false;
        }

        $money = $price->price;

        // 每份多少钱
        $pay_money = bcmul($page_total, $money, 2);

        // 多少份多少钱
        $pay_money = bcmul($pay_money, $order->print_total, 2);

        $order->page_total = $page_total;
        $order->pay_money = $pay_money;
        $order->save();
      }

      return true;
    }
    catch(\Exception $e)
    {
      record($e);

      return false;
    }
  }
}
