<?php
namespace App\Models\Api\Module\Organization;

use App\Models\Common\Module\Organization\Asset as Common;

/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2020-12-24
 *
 * 会员资产模型类
 */
class Asset extends Common
{
  // 隐藏的属性
  public $hidden = [
    'id',
    'organization_id',
    'member_id',
    'status',
    'create_time',
    'update_time'
  ];


  // 追加到模型数组表单的访问器
  protected $appends = [
    'yesterday_money',
    'current_month_money'
  ];


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-12-06
   * ------------------------------------------
   * 昨天收益封装
   * ------------------------------------------
   *
   * 昨天收益封装
   *
   * @param [type] $value [description]
   * @return [type]
   */
  public function getYesterdayMoneyAttribute($value)
  {
    $response = 0;

    $yesterday = strtotime(date('Y-m-d', strtotime('-1 day')));
    $today = strtotime(date('Y-m-d'));

    $where = [
      'member_id' => $this->member_id,
      ['create_time', '>=', $yesterday],
      ['create_time', '<', $today]
    ];

    $result = Obtain::getPluck('money', $where);

    foreach($result as $item)
    {
      $response = bcadd($response, $item, 2);
    }

    return $response;
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-12-06
   * ------------------------------------------
   * 本月收益封装
   * ------------------------------------------
   *
   * 本月收益封装
   *
   * @param [type] $value [description]
   * @return [type]
   */
  public function getCurrentMonthMoneyAttribute($value)
  {
    $response = 0;

    $before = strtotime(date('Y-m-d', strtotime('first day of this month')));
    $after = strtotime(date('Y-m-d', strtotime('first day of +1 month')));

    $where = [
      'member_id' => $this->member_id,
      ['create_time', '>=', $before],
      ['create_time', '<', $after]
    ];

    $result = Obtain::getPluck('money', $where);

    foreach($result as $item)
    {
      $response = bcadd($response, $item, 2);
    }

    return $response;
  }


  // 关联函数 ------------------------------------------------------

  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-12-06
   * ------------------------------------------
   * 机构资产与机构分红关联表
   * ------------------------------------------
   *
   * 机构资产与机构分红关联表
   *
   * @return [关联对象]
   */
  public function obtain()
  {
    return $this->hasMany(
      'App\Models\Common\Module\Organization\Obtain',
      'member_id',
      'id'
    );
  }
}
