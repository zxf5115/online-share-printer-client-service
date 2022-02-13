<?php
namespace App\Models\Api\Module;

use Illuminate\Support\Facades\DB;

use App\TraitClass\ToolTrait;
use App\Http\Constant\Parameter;
use App\TraitClass\WXBizDataCrypt;
use App\Models\Common\Module\Member as Common;


/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-06-09
 *
 * 会员模型类
 */
class Member extends Common
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
  public static function register($request, $data, $type)
  {
    DB::beginTransaction();

    try
    {
      if(1 == $type)
      {
        $model = self::firstOrNew(['open_id' => $data, 'status' => 1]);
        $model->open_id  = $data ?? '';
        $model->username = '';
      }
      else
      {
        $model = self::firstOrNew(['username' => $data, 'status' => 1]);
        $model->username  = $data ?? '';
        $model->open_id = '';
      }

      $model->role_id  = 1;
      $model->avatar   = Parameter::AVATER;
      $model->nickname = Parameter::NICKNAME . '_' . time();
      $model->save();

      $data = [
        'sex'         => $request->sex ?? '1',
        'age'         => $request->age ?? '1',
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

      return $model;
    }
    catch(\Exception $e)
    {
      DB::rollback();

      record($e);

      return false;
    }
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-06-10
   * ------------------------------------------
   * 获取openid
   * ------------------------------------------
   *
   * 获取openid
   *
   * @param string $code [description]
   * @return [type]
   */
  public static function  getUserOpenId($code)
  {
    $param = [];

    $param[] = 'appid=' . config('weixin.weixin_key');
    $param[] = 'secret=' . config('weixin.weixin_secret');
    $param[] = 'js_code=' . $code;
    $param[] = 'grant_type=authorization_code';

    $params = implode('&', $param);    //用&符号连起来

    $url = config('weixin.weixin_openid_url') . '?' . $params;

    //请求接口
    $client = new \GuzzleHttp\Client([
        'timeout' => 60
    ]);

    $res = $client->request('GET', $url);

    //openid和session_key
    return json_decode($res->getBody()->getContents(), true);
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2022-02-12
   * ------------------------------------------
   * 获取微信token信息
   * ------------------------------------------
   *
   * 获取微信token信息
   *
   * @param string $code [description]
   * @return [type]
   */
  public static function  getWeixinToken()
  {
    $param = [];

    $param[] = 'grant_type=client_credential';
    $param[] = 'appid=' . config('weixin.weixin_key');
    $param[] = 'secret=' . config('weixin.weixin_secret');

    $params = implode('&', $param);    //用&符号连起来

    $url = config('weixin.weixin_token_url') . '?' . $params;

    //请求接口
    $client = new \GuzzleHttp\Client([
        'timeout' => 60
    ]);

    $res = $client->request('GET', $url);

    return json_decode($res->getBody()->getContents(), true);
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2022-02-12
   * ------------------------------------------
   * 获取微信手机号码信息
   * ------------------------------------------
   *
   * 获取微信手机号码信息
   *
   * @param string $code [description]
   * @return [type]
   */
  public static function  getWeixinMobile($data, $iv)
  {
    $appid = config('weixin.weixin_key');
    $secret = config('weixin.weixin_secret');

    $model = new WXBizDataCrypt($appid, $secret);

    $errCode = $model->decryptData($data, $iv, $response);

    return $response;
  }


  // 关联函数 ------------------------------------------------------


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-06-08
   * ------------------------------------------
   * 学员与机构关联表
   * ------------------------------------------
   *
   * 学员与机构关联表
   *
   * @return [关联对象]
   */
  public function organization()
  {
    return $this->belongsTo(
      'App\Models\Api\Module\Organization',
      'organization_id',
      'id'
    );
  }



  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-06-08
   * ------------------------------------------
   * 会员与角色关联函数
   * ------------------------------------------
   *
   * 会员与角色关联函数
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
   * 会员与档案关联函数
   * ------------------------------------------
   *
   * 会员与档案关联函数
   *
   * @return [关联对象]
   */
  public function archive()
  {
    return $this->hasOne(
      'App\Models\Api\Module\Member\Archive',
      'member_id',
      'id'
    );
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-06-08
   * ------------------------------------------
   * 会员与资产关联表
   * ------------------------------------------
   *
   * 会员与资产关联表
   *
   * @return [关联对象]
   */
  public function asset()
  {
    return $this->hasOne(
      'App\Models\Api\Module\Member\Asset',
      'member_id',
      'id'
    );
  }
}
