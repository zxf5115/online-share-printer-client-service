<?php
namespace App\Listeners\Api\Member;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Events\Api\Member\OrderEvent;

/**
 * 订单监听器
 */
class OrderListeners
{
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
        return false;
      }

      // 打印文件页数
      $total = $order->page_total;

      // 微信支付
      if(!empty($total))
      {
        return true;
      }

      $resource = Resource::getRow(['order_id' => $order->id]);

      if(empty($resource))
      {
        return false;
      }

      $url = $resource->pdf_url;



      return $response;
    }
    catch(\Exception $e)
    {
      record($e);

      return false;
    }
  }
}
