<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Common\System\File;

/**
 * 客户上传文件处理队列
 */
class FileQueue implements SelfHandling, ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * The number of times the job may be attempted.
   *
   * @var int
   */
  public $tries = 5;


  /**
   * The number of seconds the job can run before timing out.
   *
   * @var int
   */
  public $timeout = 120;


  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($file)
  {
    $this->file = $file;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    try
    {
      $file = json_decode($this->file);

      // 文件转换为PDF
      $url = $this->conversion($file);


    }
    catch(\Exception $e)
    {
      record($e);
    }
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2022-01-04
   * ------------------------------------------
   * 将非PDF文件转换为PDF文件
   * ------------------------------------------
   *
   * 将非PDF文件转换为PDF文件
   *
   * @param [type] $file 文件对象
   * @return [type]
   */
  private function conversion($file)
  {
    // 获取文件后缀
    $extension = $file->getClientOriginalExtension();

    // 上传文件到本地
    $url = $this->file($file);

    if('pdf' == $extension)
    {
      return $url;
    }
    else if('png' == $extension || 'jpg' == $extension || 'jpeg' == $extension)
    {
      $pdf = substr($url, 0, strpos($url, '.')) . '.pdf';

      $exec = 'convert ' . $url . ' ' . $pdf;

      @exec($exec, $result, $status);

      if(0 < $status)
      {
        Log::error('file conversion error');

        return false;
      }

      return $pdf;
    }
    else
    {
      $pdf = substr($url, 0, strpos($url, '.')) . '.pdf';

      $exec = 'soffice --headless --convert-to pdf ' . $url . ' --outdir ./';

      @exec($exec, $result, $status);

      if(0 < $status)
      {
        Log::error('file conversion error');

        return false;
      }

      return $pdf;
    }
  }

  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2022-01-04
   * ------------------------------------------
   * 根据开始页数和结束页数进行文件切割
   * ------------------------------------------
   *
   * 根据开始页数和结束页数进行文件切割
   *
   * @param [type] $file 文件地址
   * @param [type] $start 开始页数
   * @param [type] $end 结束页数
   * @return [type]
   */
  public function separate($file, $start, $end)
  {
    $pdf = substr($url, 0, strpos($url, '.')) . '_' . $start . '_'. $end .'.pdf';

    // $exec = 'mutool convert -o image%d.png file.pdf 1-10';
    $exec = 'mutool convert -o ' . $pdf . ' ' . $file . ' ' . $start . '-' . $end;

    @exec($exec, $result, $status);

    if(0 < $status)
    {
      Log::error('file separate error');

      return false;
    }
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2022-01-04
   * ------------------------------------------
   * 上传文件
   * ------------------------------------------
   *
   * 上传文件
   *
   * @param string $file 文件对象
   * @param string $path 路径
   * @return [type]
   */
  public static function file($file, $path = 'temporary')
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
