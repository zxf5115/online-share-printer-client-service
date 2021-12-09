<?php
namespace App\Http\Controllers\Api\Module;

use Illuminate\Http\Request;

use App\Http\Constant\Code;
use App\Http\Controllers\Api\BaseController;


/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-11-26
 *
 * 打印价格控制器类
 */
class PriceController extends BaseController
{
  // 模型名称
  protected $_model = 'App\Models\Api\Module\Price';

  // 排序方式
  protected $_order = [
    ['key' => 'sort', 'value' => 'desc'],
    ['key' => 'create_time', 'value' => 'desc'],
  ];


  /**
   * @api {get} /api/price/select 01. 打印价格数据
   * @apiDescription 获取打印价格数据
   * @apiGroup 11. 打印价格模块
   *
   * @apiSuccess (字段说明) {Number} id 打印价格编号
   * @apiSuccess (字段说明) {String} title 价格标题
   * @apiSuccess (字段说明) {String} price 打印价格
   *
   * @apiSampleRequest /api/price/select
   * @apiVersion 1.0.0
   */
  public function select(Request $request)
  {
    try
    {
      $condition = self::getSimpleWhereData();

      // 对用户请求进行过滤
      $filter = $this->filter($request->all());

      $condition = array_merge($condition, $this->_where, $filter);

      // 获取关联对象
      $relevance = self::getRelevanceData($this->_relevance, 'select');

      $response = $this->_model::getList($condition, $relevance, $this->_order);

      return self::success($response);
    }
    catch(\Exception $e)
    {
      // 记录异常信息
      self::record($e);

      return self::error(Code::ERROR);
    }
  }


  /**
   * @api {get} /api/price/view/{id} 02. 打印价格详情
   * @apiDescription 获取打印价格详情
   * @apiGroup 11. 打印价格模块
   *
   * @apiSuccess (字段说明) {Number} id 打印价格编号
   * @apiSuccess (字段说明) {String} title 价格标题
   * @apiSuccess (字段说明) {String} price 打印价格
   *
   * @apiSampleRequest /api/price/view/{id}
   * @apiVersion 1.0.0
   */
  public function view(Request $request, $id)
  {
    try
    {
      $condition = self::getSimpleWhereData($id);

      // 获取关联对象
      $relevance = self::getRelevanceData($this->_relevance, 'view');

      $response = $this->_model::getRow($condition, $relevance);

      return self::success($response);
    }
    catch(\Exception $e)
    {
      // 记录异常信息
      self::record($e);

      return self::error(Code::ERROR);
    }
  }
}
