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


  public static function getPageTotal($path)
  {
    // 打开文件
    if (!$fp = @fopen($path, 'r'))
    {
      $error = '打开文件{$path}失败';

      return false;
    }
    else
    {
      $max=0;

      while(!feof($fp))
      {
        $line = fgets($fp, 255);

        if(preg_match('/\/Count [0-9]+/', $line, $matches))
        {
          preg_match('/[0-9]+/',$matches[0], $matches2);

          if($max < $matches2[0])
            $max=$matches2[0];
        }
      }

      fclose($fp);

      // 返回页数
      return $max;
    }
  }



  public function total($url)
  {
    $url = str_replace('storage', 'storage/app/public', $url);

    $url = base_path() . $url;

    $exec = 'pdfinfo -rawdates  ' . $url;

    // 记录转换命令
    Log::info($exec);

    exec($exec, $result, $status);

    if(0 < $status)
    {
      Log::error('file conversion error');

      return false;
    }

    if(preg_match("/Pages:\s*(.+)/i", $result, $matches) === 1)
    {
      $response = $matches[1];
      break;
    }

    return $response;
  }
}
