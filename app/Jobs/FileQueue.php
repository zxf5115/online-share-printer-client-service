<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * 客户上传文件处理队列
 */
class FileQueue implements ShouldQueue
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


  private $_url = null;

  private $_extension = '';


  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($url, $extension)
  {
    $this->_url = $url;
    $this->_extension = $extension;
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
      // 文件转换为PDF
      $url = $this->conversion($this->_url, $this->_extension);


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
   * @param [type] $extension 文件后缀
   * @return [type]
   */
  private function conversion($url, $extension)
  {
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
}
