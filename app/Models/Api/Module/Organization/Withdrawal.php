<?php
namespace App\Models\Api\Module\Organization;

use App\Models\Common\Module\Organization\Withdrawal as Common;

/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-11-23
 *
 * 机构提现模型类
 */
class Withdrawal extends Common
{
  // 隐藏的属性
  public $hidden = [
    'organization_id',
    'member_id',
    'pay_type',
    'confirm_status',
    'status',
    'update_time'
  ];
}
