<?php
namespace App\Models\Common\Module\Printer;

use App\Models\Base;

/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-09-15
 *
 * 打印机日志模型类
 */
class Log extends Base
{
  // 表名
  public $table = 'module_printer_log';

  // 隐藏的属性
  public $hidden = [
    'organization_id',
    'status',
    'update_time'
  ];

  // 追加到模型数组表单的访问器
  public $appends = [];

  /**
   * 可以被批量赋值的属性
   */
  public $fillable = ['id'];



  // 关联函数 ------------------------------------------------------


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-09-15
   * ------------------------------------------
   * 打印机日志与打印机关联函数
   * ------------------------------------------
   *
   * 打印机日志与打印机关联函数
   *
   * @return [type]
   */
  public function printer()
  {
    return $this->belongsTo(
      'App\Models\Common\Module\Printer',
      'printer_id',
      'id'
    );
  }
}
