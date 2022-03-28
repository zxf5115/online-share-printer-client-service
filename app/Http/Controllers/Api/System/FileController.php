<?php
namespace App\Http\Controllers\Api\System;

use Illuminate\Http\Request;

use App\Jobs\FileQueue;
use zxf5115\Upload\File;
use App\Http\Constant\Code;
use App\TraitClass\ToolTrait;
use App\Http\Controllers\Api\BaseController;
use App\Models\Common\System\File as LocalFile;


/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-01-04
 *
 * 文件上传接口控制器类
 */
class FileController extends BaseController
{
  use ToolTrait;

  /**
   * @api {post} /api/file/file 01. 上传文件
   * @apiDescription 通过文件内容进行文件上传
   * @apiGroup 03. 上传模块
   * @apiPermission jwt
   * @apiHeader {String} Authorization 身份令牌
   * @apiHeaderExample {json} Header-Example:
   * {
   *   "Authorization": "Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiO"
   * }
   *
   * @apiParam {string} file 文件数据
   * @apiParam {string} [category] 文件分类 excel word pdf video audio ...
   *
   * @apiSuccess (字段说明) {string} filename 文件名称
   * @apiSuccess (字段说明) {string} url 打印原始文件地址
   * @apiSuccess (字段说明) {string} pdf_url 打印PDF文件地址
   *
   * @apiSampleRequest /api/file/file
   * @apiVersion 1.0.0
   */
  public function file(Request $request)
  {
    try
    {
      $category = $request->category ?? 'picture';

      $allow = ['docx', 'doc', 'xls', 'xlsx', 'pdf', 'txt', 'png', 'jpg', 'jpeg'];

      $url = File::file_base64($request->file, $category, $allow);

      // 如果返回错误代码
      if(false === strpos($url, 'http'))
      {
        return self::message($url);
      }

      $result = LocalFile::file_base64($request->file, 'temporary');

      // 获取文件名称
      $filename = $file->getClientOriginalName();

      // 获取文件后缀
      $extension = $file->getClientOriginalExtension();

      // 将图片添加到文件队列
      FileQueue::dispatch($result, $extension);

      // 修改文件后缀
      $result = self::changeFileExtension($result);

      $response = [
        'url' => $url,
        'pdf_url' => $result,
        'filename' => $filename
      ];

      return self::success($response);
    }
    catch(\Exception $e)
    {
      // 记录异常信息
      record($e);

      return self::error(Code::FILE_UPLOAD_ERROR);
    }
  }


  /**
   * @api {post} /api/file/picture 02. 上传图片
   * @apiDescription 图片上传
   * @apiGroup 03. 上传模块
   * @apiPermission jwt
   * @apiHeader {String} Authorization 身份令牌
   * @apiHeaderExample {json} Header-Example:
   * {
   *   "Authorization": "Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiO"
   * }
   *
   * @apiParam {string} file 图片数据
   * @apiParam {string} [category] 图片分类 picture avatar ...
   *
   * @apiSuccess (字段说明) {string} data 图片地址
   *
   * @apiSampleRequest /api/file/picture
   * @apiVersion 1.0.0
   */
  public function picture(Request $request)
  {
    try
    {
      $category = $request->category ?? 'picture';

      $response = File::picture('file', $category);

      // 如果返回错误代码
      if(false === strpos($response, 'http'))
      {
        return self::message($response);
      }

      return self::success($response);
    }
    catch(\Exception $e)
    {
      // 记录异常信息
      record($e);

      return self::error(Code::FILE_UPLOAD_ERROR);
    }
  }
}
