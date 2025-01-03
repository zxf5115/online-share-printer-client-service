<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\TraitClass\ToolTrait;

/**
 * 客户上传文件处理队列
 */
class FileQueue implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ToolTrait;

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
      $pdf = $this->conversion($this->_url, $this->_extension);

      // 将PDF文件最大按照10页分割成多个小PDF
      $this->separate($pdf, 10);
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
    else if('png' == $extension || 'jpg' == $extension || 'jpeg' == $extension || 'gif' == $extension || 'bmp' == $extension || 'tif' == $extension || 'jpeg2000' == $extension)
    {
      $url = str_replace('storage', 'storage/app/public', $url);

      $url = base_path() . $url;

      $pdf = substr($url, 0, strrpos($url, '.')) . '.pdf';

      $exec = 'convert ' . $url . ' ' . $pdf;

      // 记录转换命令
      Log::info($exec);

      exec($exec, $result, $status);

      if(0 < $status)
      {
        Log::error('file conversion error');

        return false;
      }

      return $pdf;
    }
    else
    {
      $url = str_replace('storage', 'storage/app/public', $url);

      $url = base_path() . $url;

      $directory = substr($url, 0, strrpos($url, '/'));

      $pdf = substr($url, 0, strrpos($url, '.')) . '.pdf';

      $exec = 'soffice --headless --convert-to pdf ' . $url . ' --outdir ' . $directory;

      // 记录转换命令
      Log::info($exec);

      exec($exec, $result, $status);

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
   * @param [type] $url 文件地址
   * @param [type] $start 分割页数
   * @return [type]
   */
  public function separate($url, $page = 10)
  {
    $execs = [];

    // 计算PDF页数
    $page_total = self::getPageTotal($url, false);

    for($i = 1; $i < 20; $i++)
    {
      $total = bcdiv($page_total, $i);

      if(10 > $total)
      {
        break;
      }
    }

    for($x = 0; $x < $i; $x++)
    {
      $page = bcmul($x, 10);

      $start = bcadd($page, 1);

      $end = bcadd($page, 10);

      $end = $end > $page_total ? $page_total : $end;

      $file = substr($url, 0, strrpos($url, '.')) . '_' . $start . '_'. $end .'.pdf';

      // $exec = 'mutool convert -o image%d.png file.pdf 1-10';
      $execs[] = 'mutool convert -o ' . $file . ' ' . $url . ' ' . $start . '-' . $end;
    }

    // 分割文件
    foreach($execs as $exec)
    {
      exec($exec, $result, $status);

      if(0 < $status)
      {
        Log::error('file separate error');

        return false;
      }
    }
  }
}
