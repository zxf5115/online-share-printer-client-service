<?php
namespace App\TraitClass;

/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2020-10-22
 *
 * 工具特征
 */
trait ToolTrait
{

  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-12-16
   * ------------------------------------------
   * 合成订单编号
   * ------------------------------------------
   *
   * 合成订单编号
   *
   * @param string $prefix 订单前缀
   * @return [type]
   */
  public static function getOrderNo($prefix = 'SO')
  {
    $rand = str_pad(rand(1, 999999), 6, 0, STR_PAD_LEFT);

    return $prefix . date('YmdHis') . $rand;
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-07-01
   * ------------------------------------------
   * 将结果集按照指定字段进行分组
   * ------------------------------------------
   *
   * 将结果集按照指定字段进行分组
   *
   * @return [type]
   */
  public static function allocation($data, $field)
  {
    $response = [];

    $result = [];

    if(empty($data['data']))
    {
      return $data;
    }

    $allocation = array_column($data['data'], $field);

    foreach($data['data'] as $k => $item)
    {
      if(false !== $key = array_search($item[$field], $allocation))
      {
        $result[$key][] = $item;
      }
    }

    foreach($result as $k => $item)
    {
      $response[$k]['time'] = $allocation[$k];
      $response[$k]['data'] = $item;
    }

    $response = array_values($response);

    $data['data'] = $response;

    return $data;
  }
}
