<?php
namespace App\Models\Api\Module;

use App\Models\Common\Module\Price as Common;

/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-11-26
 *
 * 价格模型类
 */
class Price extends Common
{
  // 隐藏的属性
  public $hidden = [
    'organization_id',
    'sort',
    'status',
    'create_time',
    'update_time'
  ];

}
