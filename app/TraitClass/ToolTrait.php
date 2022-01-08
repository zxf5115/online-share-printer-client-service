<?php
namespace App\TraitClass;

use Illuminate\Support\Facades\Log;

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


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2022-01-06
   * ------------------------------------------
   * 获取PDF文件页数
   * ------------------------------------------
   *
   * 获取PDF文件页数
   *
   * @param [type] $url 文件地址
   * @return [type]
   */
  public static function getPageTotal($url, $is_all = true)
  {
    if($is_all)
    {
      $url = str_replace('storage', 'storage/app/public', $url);

      $url = base_path() . $url;
    }

    $exec = 'pdfinfo -rawdates  ' . $url;

    // 记录转换命令
    Log::info($exec);

    exec($exec, $result, $status);

    if(0 < $status)
    {
      Log::error('file conversion error');

      return false;
    }

    foreach($result as $item)
    {
      if(preg_match("/Pages:\s*(.+)/i", $item, $matches) === 1)
      {
        $response = $matches[1];

        break;
      }
    }

    return $response;
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2022-01-06
   * ------------------------------------------
   * 修改文件后缀
   * ------------------------------------------
   *
   * 修改文件后缀
   *
   * @param [type] $url 文件地址
   * @param [type] $extension 文件后缀
   * @return [type]
   */
  public static function changeFileExtension($url, $extension = '.pdf')
  {
    return substr($url, 0, strrpos($url, '.')) . $extension;
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2022-01-07
   * ------------------------------------------
   * 将大PDF文件分割成多个小PDF文件，返回文件地址
   * ------------------------------------------
   *
   * 将大PDF文件分割成多个小PDF文件，返回文件地址
   *
   * @param [type] $url 大PDF文件地址
   * @return [type]
   */
  public static function getSeparateFileUrl($url, $threshold = 10)
  {
    $response = [];

    // 计算PDF页数
    $page_total = self::getPageTotal($url);

    for($i = 1; $i < 20; $i++)
    {
      $total = bcdiv($page_total, $i);

      if($threshold > $total)
      {
        break;
      }
    }

    for($x = 0; $x < $i; $x++)
    {
      $page = bcmul($x, 10);

      $start = bcadd($page, 1);

      $end = bcadd($page, 10);

      $response[] = substr($url, 0, strpos($url, '.')) . '_' . $start . '_'. $end .'.pdf';
    }

    return $response;
  }
}
