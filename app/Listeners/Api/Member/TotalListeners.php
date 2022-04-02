<?php
namespace App\Listeners\Api\Member;

use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\TraitClass\ToolTrait;
use App\Models\Api\Module\Price;
use App\Events\Api\Member\TotalEvent;
use App\Models\Api\Module\Order\Resource;

/**
 * 打印文件页数监听器
 */
class TotalListeners
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
   * @param  TotalEvent  $event
   * @return void
   */
  public function handle(TotalEvent $event)
  {
    try
    {
      // 订单信息
      $order = $event->order;

      // 打印文件
      $url = $event->url;

      if(empty($order->id))
      {
        Log::error('订单异常(订单数据为空)');

        return false;
      }

      $page_total = 0;

      if(!empty($url))
      {
        // 计算PDF页数
        $page_total = self::getPageTotal($url);

        $order->page_total = $page_total;
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
