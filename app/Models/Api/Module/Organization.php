<?php
namespace App\Models\Api\Module;

use Illuminate\Support\Facades\DB;

use App\TraitClass\ToolTrait;
use App\Http\Constant\Parameter;
use App\Models\Common\Module\Organization\Obtain;
use App\Models\Common\Module\Organization as Common;


/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-06-09
 *
 * 机构模型类
 */
class Organization extends Common
{
  // 隐藏的属性
  public $hidden = [
    'organization_id',
    'open_id',
    'password',
    'password_salt',
    'remember_token',
    'last_login_time',
    'last_login_ip',
    'try_number',
    'status',
    'create_time',
    'update_time'
  ];


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-07-01
   * ------------------------------------------
   * 自动注册
   * ------------------------------------------
   *
   * 自动注册
   *
   * @return [type]
   */
  public static function register($param, $value)
  {
    DB::beginTransaction();

    try
    {
      $model = self::firstOrNew([$param => $value]);

      $model->nickname = Parameter::NICKNAME . '_' . time();
      $model->role_id  = 1;
      $model->avatar   = Parameter::AVATER;
      $model->password = self::generate(Parameter::PASSWORD);
      $model->save();

      $data = [
        'sex'         => 1,
        'age'         => 1,
        'province_id' => $request->province_id ?? '',
        'city_id'     => $request->city_id ?? '',
        'region_id'   => $request->region_id ?? '',
        'address'     => $request->address ?? '',
      ];

      if(!empty($data))
      {
        $model->archive()->delete();
        $model->archive()->create($data);
      }

      $data = [
        'money' => 0.00,
      ];

      if(!empty($data))
      {
        $model->asset()->delete();
        $model->asset()->create($data);
      }

      DB::commit();
    }
    catch(\Exception $e)
    {
      DB::rollback();

      record($e);

      return false;
    }
  }


  // 关联函数 ------------------------------------------------------


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-09-17
   * ------------------------------------------
   * 机构与上级机构关联函数
   * ------------------------------------------
   *
   * 机构与上级机构关联函数
   *
   * @return [关联对象]
   */
  public function parent()
  {
    return $this->belongsTo(
      'App\Models\Api\Module\Organization',
      'parent_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-09-24
   * ------------------------------------------
   * 机构与下级机构关联函数
   * ------------------------------------------
   *
   * 机构与下级机构关联函数
   *
   * @return [关联对象]
   */
  public function children()
  {
    return $this->hasMany(
      'App\Models\Api\Module\Organization',
      'id',
      'parent_id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-06-08
   * ------------------------------------------
   * 机构与角色关联函数
   * ------------------------------------------
   *
   * 机构与角色关联函数
   *
   * @return [关联对象]
   */
  public function role()
  {
    return $this->belongsTo(
      'App\Models\Api\Module\Member\Role',
      'role_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-06-08
   * ------------------------------------------
   * 机构与档案关联函数
   * ------------------------------------------
   *
   * 机构与档案关联函数
   *
   * @return [关联对象]
   */
  public function archive()
  {
    return $this->hasOne(
      'App\Models\Api\Module\Organization\Archive',
      'member_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-06-08
   * ------------------------------------------
   * 机构与资产关联表
   * ------------------------------------------
   *
   * 机构与资产关联表
   *
   * @return [关联对象]
   */
  public function asset()
  {
    return $this->hasOne(
      'App\Models\Api\Module\Organization\Asset',
      'member_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-06-08
   * ------------------------------------------
   * 机构与资源关联函数
   * ------------------------------------------
   *
   * 机构与资源关联函数
   *
   * @return [关联对象]
   */
  public function resource()
  {
    return $this->hasOne(
      'App\Models\Api\Module\Organization\Resource',
      'member_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-12-08
   * ------------------------------------------
   * 机构与收益关联函数
   * ------------------------------------------
   *
   * 机构与收益关联函数
   *
   * @return [关联对象]
   */
  public function obtain()
  {
    return $this->hasMany(
      'App\Models\Api\Module\Organization\Obtain',
      'member_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-09-24
   * ------------------------------------------
   * 一级代理商与机构打印机关联表
   * ------------------------------------------
   *
   * 一级代理商与机构打印机关联表
   *
   * @return [关联对象]
   */
  public function first()
  {
    return $this->hasMany(
      'App\Models\Api\Module\Printer',
      'first_level_agent_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-09-24
   * ------------------------------------------
   * 二级代理商与机构打印机关联表
   * ------------------------------------------
   *
   * 二级代理商与机构打印机关联表
   *
   * @return [关联对象]
   */
  public function second()
  {
    return $this->hasMany(
      'App\Models\Api\Module\Printer',
      'second_level_agent_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-09-24
   * ------------------------------------------
   * 店长与机构打印机关联表
   * ------------------------------------------
   *
   * 店长与机构打印机关联表
   *
   * @return [关联对象]
   */
  public function printer()
  {
    return $this->hasMany(
      'App\Models\Api\Module\Printer',
      'manager_id',
      'id'
    );
  }
}
