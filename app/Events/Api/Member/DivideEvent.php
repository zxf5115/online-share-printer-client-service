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
 * 订单分红事件
 */
class DivideEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $order = null;

  /**
   * Create a new event instance.
   *
   * @return void
   */
  public function __construct($order)
  {
    $this->order = $order;
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
