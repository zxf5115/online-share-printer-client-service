<?php
namespace App\Models\Api\Module;

use App\Models\Common\Module\Order as Common;

/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-06-29
 *
 * 课程订单模型类
 */
class Order extends Common
{
  // 隐藏的属性
  public $hidden = [
    'organization_id',
    'status',
    'update_time'
  ];

  // 追加到模型数组表单的访问器
  protected $appends = [
    'all_page_total'
  ];


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2020-12-20
   * ------------------------------------------
   * 阅读状态封装
   * ------------------------------------------
   *
   * 阅读状态封装
   *
   * @param [type] $value [description]
   * @return [type]
   */
  public function getAllPageTotalAttribute($value)
  {
    return bcmul($this->page_total, $this->print_total);
  }


  // 关联函数 ------------------------------------------------------


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-06-29
   * ------------------------------------------
   * 订单与一级代理商关联函数
   * ------------------------------------------
   *
   * 订单与一级代理商关联函数
   *
   * @return [关联对象]
   */
  public function first()
  {
    return $this->belongsTo(
      'App\Models\Api\Module\Organization',
      'first_level_agent_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-06-29
   * ------------------------------------------
   * 订单与二级代理商关联函数
   * ------------------------------------------
   *
   * 订单与二级代理商关联函数
   *
   * @return [关联对象]
   */
  public function second()
  {
    return $this->belongsTo(
      'App\Models\Api\Module\Organization',
      'second_level_agent_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-06-29
   * ------------------------------------------
   * 订单与店长关联函数
   * ------------------------------------------
   *
   * 订单与店长关联函数
   *
   * @return [关联对象]
   */
  public function manager()
  {
    return $this->belongsTo(
      'App\Models\Api\Module\Organization',
      'manager_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-11-29
   * ------------------------------------------
   * 订单与会员关联函数
   * ------------------------------------------
   *
   * 订单与会员关联函数
   *
   * @return [关联对象]
   */
  public function member()
  {
    return $this->belongsTo(
      'App\Models\Api\Module\Member',
      'member_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-11-29
   * ------------------------------------------
   * 订单与打印机关联函数
   * ------------------------------------------
   *
   * 订单与打印机关联函数
   *
   * @return [关联对象]
   */
  public function printer()
  {
    return $this->belongsTo(
      'App\Models\Api\Module\Printer',
      'printer_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-11-29
   * ------------------------------------------
   * 订单与打印价格关联函数
   * ------------------------------------------
   *
   * 订单与打印价格关联函数
   *
   * @return [关联对象]
   */
  public function price()
  {
    return $this->belongsTo(
      'App\Models\Api\Module\Price',
      'type',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-12-18
   * ------------------------------------------
   * 订单与订单资源关联函数
   * ------------------------------------------
   *
   * 订单与订单资源关联函数
   *
   * @return [关联对象]
   */
  public function resource()
  {
    return $this->hasOne(
      'App\Models\Api\Module\Order\Resource',
      'order_id',
      'id',
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-11-08
   * ------------------------------------------
   * 订单与订单日志关联函数
   * ------------------------------------------
   *
   * 订单与订单日志关联函数
   *
   * @return [关联对象]
   */
  public function log()
  {
    return $this->hasMany(
      'App\Models\Api\Module\Order\Log',
      'order_id',
      'id',
    );
  }
}
