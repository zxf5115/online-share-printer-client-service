<?php
namespace App\Events\Api\Member;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * 打印文件页数事件
 */
class TotalEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $order = null;

  public $url = null;

  /**
   * Create a new event instance.
   *
   * @return void
   */
  public function __construct($order, $url)
  {
    $this->order = $order;

    $this->url = $url;
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return \Illuminate\Broadcasting\Channel|array
   */
  public function broadcastOn()
  {
    return new PrivateChannel('channel-name');
  }
}
