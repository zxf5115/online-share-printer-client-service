<?php
namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
          'SocialiteProviders\Weixin\WeixinExtendSocialite@handle',
        ],

        // 发送短信
        'App\Events\Common\Message\SmsEvent' => [
            'App\Listeners\Common\Message\SmsListeners',
        ],

        // 短信验证码
        'App\Events\Common\Sms\CodeEvent' => [
            'App\Listeners\Common\Sms\CodeListeners',
        ],

        // 发送邮件
        'App\Events\Common\Message\EmailEvent' => [
            'App\Listeners\Common\Message\EmailListeners',
        ],

        // 发送邮件
        'App\Events\Common\Push\AuroraEvent' => [
            'App\Listeners\Common\Push\AuroraListeners',
        ],

        // 系统通知
        'App\Events\Common\NoticeEvent' => [
            'App\Listeners\Common\NoticeListeners',
        ],

        // 支付
        'App\Events\Api\Member\PayEvent' => [
            'App\Listeners\Api\Member\PayListeners',
        ],

        // 打印文件页数
        'App\Events\Api\Member\TotalEvent' => [
            'App\Listeners\Api\Member\TotalListeners',
        ],

        // 订单
        'App\Events\Api\Member\OrderEvent' => [
            'App\Listeners\Api\Member\OrderListeners',
        ],

        // 分红
        'App\Events\Api\Member\DivideEvent' => [
            'App\Listeners\Api\Member\DivideListeners',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
