<?php
namespace App\Models\Api\Module;

use App\Models\Common\Module\Outbound as Common;

/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-09-17
 *
 * 出库单模型类
 */
class Outbound extends Common
{
  // 隐藏的属性
  public $hidden = [
    'organization_id',
    'active',
    'update_time'
  ];


  // 关联函数 ------------------------------------------------------

  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-09-13
   * ------------------------------------------
   * 出库与出库详情关联函数
   * ------------------------------------------
   *
   * 出库与出库详情关联函数
   *
   * @return [关联对象]
   */
  public function detail()
  {
    return $this->hasMany(
      'App\Models\Api\Module\Outbound\Detail',
      'outbound_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-09-13
   * ------------------------------------------
   * 出库与出库资源关联函数
   * ------------------------------------------
   *
   * 出库与出库资源关联函数
   *
   * @return [关联对象]
   */
  public function resource()
  {
    return $this->hasOne(
      'App\Models\Api\Module\Outbound\Resource',
      'outbound_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-11-12
   * ------------------------------------------
   * 出库与出库物流关联函数
   * ------------------------------------------
   *
   * 出库与出库物流关联函数
   *
   * @return [关联对象]
   */
  public function logistics()
  {
    return $this->hasOne(
      'App\Models\Api\Module\Outbound\Logistics',
      'outbound_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-09-13
   * ------------------------------------------
   * 出库与代理商关联表
   * ------------------------------------------
   *
   * 出库与代理商关联表
   *
   * @return [关联对象]
   */
  public function member()
  {
    return $this->belongsTo(
      'App\Models\Api\Module\Organization',
      'member_id',
      'id'
    );
  }
}
