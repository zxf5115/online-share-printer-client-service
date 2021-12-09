<?php
namespace App\Models\Api\Module\Organization;

use App\Enum\Common\TimeEnum;
use App\Models\Common\Module\Organization\Obtain as Common;

/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-11-23
 *
 * 机构分红模型类
 */
class Obtain extends Common
{
  // 隐藏的属性
  public $hidden = [
    'organization_id',
    'member_id',
    'confirm_status',
    'status',
    'update_time'
  ];

  // 附加数据
  protected $appends = [
    'datetime',
  ];


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-07-01
   * ------------------------------------------
   * 时间封装
   * ------------------------------------------
   *
   * 时间封装
   *
   * @param int $value [数据库存在的值]
   * @return 状态值
   */
  public function getDateTimeAttribute($value)
  {
    $datetime  = date('y年m月 ', strtotime($this->create_time));

    $week = TimeEnum::formatWeek(strtotime($this->create_time));

    return $datetime.$week;
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-11-08
   * ------------------------------------------
   * 获得收益金额
   * ------------------------------------------
   *
   * 获得收益金额
   *
   * @param [type] $value [description]
   * @return [type]
   */
  public static function getObtainMoney($response, $request)
  {
    if(empty($response['data']))
    {
      return $response;
    }

    foreach($response['data'] as $key => $item)
    {
      $money = 0.00;

      $where = ['member_id' => $item['id']];

      if(!empty($request['create_time']))
      {
        $where[] = ['create_time', '>=', $request['create_time'][0]];
        $where[] = ['create_time', '<=', $request['create_time'][1]];
      }

      $result = self::getPluck('money', $where, false, false, true);

      foreach($result as $item)
      {
        $money = bcadd($money, $item, 2);
      }

      $response['data'][$key]['obtain_money'] = $money;
    }

    return $response;
  }


  // 关联函数 ------------------------------------------------------

  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-09-25
   * ------------------------------------------
   * 代理商收益与代理商关联表
   * ------------------------------------------
   *
   * 代理商收益与代理商关联表
   *
   * @return [关联对象]
   */
  public function organization()
  {
    return $this->belongsTo(
      'App\Models\Api\Module\Organization',
      'member_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-11-25
   * ------------------------------------------
   * 代理商收益与订单关联表
   * ------------------------------------------
   *
   * 代理商收益与订单关联表
   *
   * @return [关联对象]
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
