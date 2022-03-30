<?php
namespace App\Models\Common\System;

use Illuminate\Support\Facades\Storage;

use App\Models\Base;
use App\Http\Constant\Code;


/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2020-07-14
 *
 * 文件上传模型
 */
class File extends Base
{
  public $ossClient = null;

  public $req = null;

  public $data = [];

  /**
   * 允许类型
   */
  public $allow = ['jpeg','png','gif','jpg'];

  /**
   * 水印图片地址
   */
  public $water = '';

  /**
   * 保存目录
   */
  public $dir = '/shop/';

  /**
   * 图片宽度
   */
  public $width = 800;

  /**
   * 图片高度
   */
  public $height = 600;



  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2020-02-24
   * ------------------------------------------
   * 上传图片
   * ------------------------------------------
   *
   * 上传图片
   *
   * @param string $name 文件名
   * @param string $path 路径
   * @param boolean $type 是否支持上传服务器，默认不上传
   * @param string $disk 用那种方式上传 oss, cos, qiniu, 又拍云
   * @param array $extension 允许上传的后缀
   * @return [type]
   */
  public static function image($name, $path = 'uploads', $disk = 'public', $extension = [])
  {
    $allowExtension = [
      'jpg',
      'jpeg',
      'png',
      'gif',
      'bmp'
    ];

    if($extension)
      $allowExtension = array_merge($allowExtension, $extension);

    return self::file($name, $path, $disk, $allowExtension);
  }



  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2020-02-24
   * ------------------------------------------
   * 上传PDF
   * ------------------------------------------
   *
   * 上传PDF
   *
   * @param string $name 文件名
   * @param string $path 路径
   * @param boolean $type 是否支持上传服务器，默认不上传
   * @param string $disk 用那种方式上传 oss, cos, qiniu, 又拍云
   * @param array $extension 允许上传的后缀
   * @return [type]
   */
  public static function pdf($name, $path = 'uploads', $disk = 'public', $extension = [])
  {
    return self::file($name, $path, $disk);
  }



  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2020-02-24
   * ------------------------------------------
   * 上传文件
   * ------------------------------------------
   *
   * 上传文件
   *
   * @param string $name 文件名
   * @param string $path 路径
   * @param string $disk 用那种方式上传 oss, cos, qiniu, 又拍云
   * @param array $extension 允许上传的后缀
   * @return [type]
   */
  public static function file($name, $path = 'uploads', $disk = 'public', $allowExtension = [])
  {
    if (!request()->hasFile($name))
    {
      return [
        'status' => Code::FILE_UPLOAD_EXIST,
        'message' => Code::$message[Code::FILE_UPLOAD_EXIST]
      ];
    }

    $file = request()->file($name);

    if(!$file->isValid())
    {
      return [
        'status' => Code::FILE_UPLOAD_FAILURE_RETRY,
        'message' => Code::$message[Code::FILE_UPLOAD_FAILURE_RETRY]
      ];
    }

    // 过滤所有的.符号
    $path = str_replace('.', '', $path);

      // 先去除两边空格
    $path = trim($path, '/');

      // 获取文件后缀
    $extension = strtolower($file->getClientOriginalExtension());

    // 组合新的文件名
    $newName = md5(time()).'.'.$extension;

    // 获取上传的文件名
    $oldName = $file->getClientOriginalName();

    if (!empty($allowExtension) && !in_array($extension, $allowExtension))
    {
      return [
        'status' => Code::FILE_UPLOAD_EXTENSION_ERROR,
        'message' => Code::$message[Code::FILE_UPLOAD_EXTENSION_ERROR]
      ];
    }

    $dir = $path . DIRECTORY_SEPARATOR . date('Y-m-d');

    Storage::disk($disk)->makeDirectory($dir);

    $filename = $dir . DIRECTORY_SEPARATOR . $newName;

    if(Storage::disk($disk)->put($filename, file_get_contents($file)))
    {
      return [
        'url' => Storage::url($filename),
        'filename' => $oldName
      ];
    }
    else
    {
      return false;
    }
  }



  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2020-02-24
   * ------------------------------------------
   * 上传文件（base64）
   * ------------------------------------------
   *
   * 上传文件（base64）
   *
   * @param string $file 数据内容
   * @param string $path 路径
   * @param string $disk 用那种方式上传 oss, cos, qiniu, 又拍云
   * @param array $extension 允许上传的后缀
   * @return [type]
   */
  public static function file_base64($file, $path = 'uploads', $disk = 'public', $allowExtension = [])
  {
    try
    {
      // 判断当前资源是什么
      if(false !==strpos($file, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'))
      {
        // 替换编码头
        preg_match('/^(data:application\/vnd.openxmlformats-officedocument.wordprocessingml.document;base64,)/', $file, $data);
        $data[2] = 'docx';
      }
      else if(false !==strpos($file, 'application/msword'))
      {
        // 替换编码头
        preg_match('/^(data:application\/msword;base64,)/', $file, $data);
        $data[2] = 'doc';
      }
      else if(false !==strpos($file, 'application/vnd.ms-excel application/x-excel'))
      {
        // 替换编码头
        preg_match('/^(data:application\/vnd.ms-excel application\/x-excel;base64,)/', $file, $data);
        $data[2] = 'xls';
      }
      else if(false !==strpos($file, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'))
      {
        // 替换编码头
        preg_match('/^(data:application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,)/', $file, $data);
        $data[2] = 'xlsx';
      }
      else if(false !==strpos($file, 'application/vnd.ms-powerpoint'))
      {
        // 替换编码头
        preg_match('/^(data:application\/vnd.ms-powerpoint;base64,)/', $file, $data);
        $data[2] = 'ppt';
      }
      else if(false !==strpos($file, 'application/vnd.openxmlformats-officedocument.presentationml.presentation'))
      {
        // 替换编码头
        preg_match('/^(data:application\/vnd.openxmlformats-officedocument.presentationml.presentation;base64,)/', $file, $data);
        $data[2] = 'pptx';
      }
      else if(false !==strpos($file, 'application/pdf'))
      {
        // 替换编码头
        preg_match('/^(data:application\/pdf;base64,)/', $file, $data);
        $data[2] = 'pdf';
      }
      else if(false !==strpos($file, 'application/octet-stream'))
      {
        // 替换编码头
        preg_match('/^(data:application\/octet-stream;base64,)/', $file, $data);
        $data[2] = 'xlsx';
      }
      else if(false !==strpos($file, 'audio/mp3'))
      {
        // 替换编码头
        preg_match('/^(data:audio\/mp3;base64,)/', $file, $data);
        $data[2] = 'mp3';
      }
      else if(false !==strpos($file, 'text/plain'))
      {
        // 替换编码头
        preg_match('/^(data:text\/plain;base64,)/', $file, $data);
        $data[2] = 'txt';
      }
      else
      {
        // 替换编码头
        preg_match('/^(data:\s*image\/(\w+);base64,)/', $file, $data);
      }

      $file = base64_decode(str_replace($data[1], '', $file));

      // 过滤所有的.符号
      $path = str_replace('.', '', $path);

        // 先去除两边空格
      $path = trim($path, '/');

        // 获取文件后缀
      $extension = $data[2];

      $filename = time() . mt_rand(1, 9999999);

        // 组合新的文件名
      $newName = md5($filename).'.'.$extension;

      $dir = $path . DIRECTORY_SEPARATOR . date('Y-m-d');

      Storage::disk($disk)->makeDirectory($dir);

      $filename = $dir . DIRECTORY_SEPARATOR . $newName;

      if(Storage::disk($disk)->put($filename, $file))
      {
        $url = Storage::url($filename);

        return [
          'url' => $url,
          'extension' => $extension,
        ];
      }
      else
      {
        return false;
      }
    }
    catch(\Exception $e)
    {
      return false;
    }
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2022-01-04
   * ------------------------------------------
   * 上传数据
   * ------------------------------------------
   *
   * 上传数据
   *
   * @param string $file 文件对象
   * @param string $path 路径
   * @return [type]
   */
  public static function data($file, $path = 'temporary')
  {
    if(!$file->isValid())
    {
      return [
        'status' => Code::FILE_UPLOAD_FAILURE_RETRY,
        'message' => Code::$message[Code::FILE_UPLOAD_FAILURE_RETRY]
      ];
    }

    // 过滤所有的.符号
    $path = str_replace('.', '', $path);

      // 先去除两边空格
    $path = trim($path, '/');

      // 获取文件后缀
    $extension = strtolower($file->getClientOriginalExtension());

    // 组合新的文件名
    $newName = md5(time()).'.'.$extension;

    $dir = $path . DIRECTORY_SEPARATOR . date('Y-m-d');

    Storage::disk('public')->makeDirectory($dir);

    $filename = $dir . DIRECTORY_SEPARATOR . $newName;

    if(Storage::disk('public')->put($filename, file_get_contents($file)))
    {
      return Storage::url($filename);
    }
    else
    {
      return false;
    }
  }
}
