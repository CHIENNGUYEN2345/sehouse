<?php

namespace App\Http\Controllers;

use App\Mail\OrderShipped;
use Illuminate\Notifications\Notification;
use Mail;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

//use NotificationChannels\OneSignal\OneSignalMessage;

class PushController extends Notification
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function via($notifiable)
    {
        return [OneSignalChannel::class];
    }


    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject("Your haha account was approved!")
            ->body("Click here to see details.")
            ->url('http://onesignal.com')
            ->webButton(
                OneSignalWebButton::create(['05bb488d-d244-4cc8-8e0f-ba75a9b65ddb', 'e3a61bb0-3a6d-4f1e-992d-c5fe4bcf71de', '6b5aadc0-d241-42b1-be07-c37499b0f2be'])
                    ->text('Click here')
                    ->icon('https://upload.wikimedia.org/wikipedia/commons/4/4f/Laravel_logo.png')
                    ->url('http://laravel.com')
            );
    }

    public function routeNotificationForOneSignal()
    {
        return ['05bb488d-d244-4cc8-8e0f-ba75a9b65ddb', 'e3a61bb0-3a6d-4f1e-992d-c5fe4bcf71de', '6b5aadc0-d241-42b1-be07-c37499b0f2be'];
    }
}


