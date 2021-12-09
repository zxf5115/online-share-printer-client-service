<?php
namespace App\Models\Api\Module\Organization;

use App\Models\Common\Module\Organization\Printer as Common;

/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-11-23
 *
 * 机构打印机模型类
 */
class Printer extends Common
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
}
