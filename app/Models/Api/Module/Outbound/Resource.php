<?php
namespace App\Models\Api\Module\Outbound;

use App\Models\Common\Module\Outbound\Resource as Common;

/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-11-24
 *
 * 出库资源模型类
 */
class Resource extends Common
{
  // 隐藏的属性
  public $hidden = [
    'id',
    'organization_id',
    'outbound_id',
    'device_code',
    'picture',
    'status',
    'update_time'
  ];

  // 关联函数 ------------------------------------------------------


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-11-16
   * ------------------------------------------
   * 出库物流与出库关联表
   * ------------------------------------------
   *
   * 出库物流与出库关联表
   *
   * @return [关联对象]
   */
  public function outbound()
  {
    return $this->belongsTo(
      'App\Models\Api\Module\Outbound',
      'outbound_id',
      'id'
    );
  }
}
