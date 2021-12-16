<?php
namespace App\Models\Api\Module\Order;

use App\Models\Common\Module\Order\Log as Common;

/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-12-18
 *
 * 订单资源模型类
 */
class Resource extends Common
{
  // 隐藏的属性
  public $hidden = [
    'organization_id',
    'status',
    'create_time',
    'update_time'
  ];

  // 关联函数 ------------------------------------------------------


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-07-07
   * ------------------------------------------
   * 订单日志与订单关联函数
   * ------------------------------------------
   *
   * 订单日志与订单关联函数
   *
   * @return [type]
   */
  public function order()
  {
    return $this->belongsTo(
      'App\Models\Api\Module\Order',
      'order_id',
      'id'
    );
  }
}
