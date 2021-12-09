<?php
namespace App\Models\Api\Module\Outbound;

use App\Models\Common\Module\Outbound\Detail as Common;

/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-11-24
 *
 * 出库明细模型类
 */
class Detail extends Common
{
  // 隐藏的属性
  public $hidden = [
    'organization_id',
    'outbound_id',
    'member_id',
    'status',
    'create_time',
    'update_time'
  ];

  // 关联函数 ------------------------------------------------------


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-11-16
   * ------------------------------------------
   * 出库详情与出库关联表
   * ------------------------------------------
   *
   * 出库详情与出库关联表
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
