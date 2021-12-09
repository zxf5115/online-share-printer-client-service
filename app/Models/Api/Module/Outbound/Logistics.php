<?php
namespace App\Models\Api\Module\Outbound;

use App\Models\Common\Module\Logistics\Company;
use App\Models\Common\Module\Outbound\Logistics as Common;

/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-11-24
 *
 * 出库物流模型类
 */
class Logistics extends Common
{
  // 隐藏的属性
  public $hidden = [
    'id',
    'organization_id',
    'outbound_id',
    'company_id',
    'status',
    'create_time',
    'update_time'
  ];

  // 追加到模型数组表单的访问器
  protected $appends = [
    'company_name'
  ];


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-11-26
   * ------------------------------------------
   * 物流公司信息封装
   * ------------------------------------------
   *
   * 物流公司信息封装
   *
   * @param int $value 状态值
   * @return 状态信息
   */
  public function getCompanyNameAttribute($value)
  {
    $result = Company::getRow(['id' => $this->company_id]);

    if(empty($result->id))
    {
      return '未知';
    }

    return $result->name;
  }


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
