<?php

use App\User;
use App\Models\GuestDevice;
use App\Models\UserNotifications;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;

if ( ! function_exists( 'sendNotification' ) ) {
    /**
     * Get Total Refunded Amount order
     * @param $id
     *
     * @return  float|integer
     */
        function sendNotification( $user_id = null , $link, $title_notification, $text) {
        if(!$user_id)
        {
            $users = User::where('is_notified',1)->get();
            foreach($users as $user )
            {
                $data = ['link'=>$link,
                    'text'=>$text];
                UserNotifications::create(
                    [
                        'user_id'=>$user->id,
                        'link'=>$link,
                        'title' => $title_notification,
                        'text'=>$text,
                        'is_read'=>0,
                    ]
                );
                $optionBuilder = new OptionsBuilder();
                $optionBuilder->setTimeToLive(60*20);

                $notificationBuilder = new PayloadNotificationBuilder('TireShop - ');
                $notificationBuilder->setBody($text)
                    ->setSound('default');

                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData(['a_data' => $data]);

                $option = $optionBuilder->build();
                $notification = $notificationBuilder->build();
                $data = $dataBuilder->build();

                $token = $user->device_token;
                if($token) {
                    $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
                    $downstreamResponse->numberSuccess();
                    $downstreamResponse->numberFailure();
                    $downstreamResponse->numberModification();
                    // return Array - you must remove all this tokens in your database
                    $downstreamResponse->tokensToDelete();
                    // return Array (key : oldToken, value : new token - you must change the token in your database)
                    $downstreamResponse->tokensToModify();
                    // return Array - you should try to resend the message to the tokens in the array
                    $downstreamResponse->tokensToRetry();
                    // return Array (key:token, value:error) - in production you should remove from your database the tokens
                    $downstreamResponse->tokensWithError();
                }
            }
            $data = ['link'=>$link,
                'text'=>$text];

            // You must change it to get your tokens
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60*20);

            $notificationBuilder = new PayloadNotificationBuilder('TireShop - ');
            $notificationBuilder->setBody($text)
                ->setSound('default');

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData(['a_data' => $data]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $tokens = GuestDevice::get()->pluck('device_token')->toArray();

            if($tokens){
                $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();
                // return Array - you must remove all this tokens in your database
                $downstreamResponse->tokensToDelete();
                // return Array (key : oldToken, value : new token - you must change the token in your database)
                $downstreamResponse->tokensToModify();
                // return Array - you should try to resend the message to the tokens in the array
                $downstreamResponse->tokensToRetry();
                // return Array (key:token, value:error) - in production you should remove from your database the tokens present in this array
                $downstreamResponse->tokensWithError();
            }
        }
        else {
            $title = 'Embadal - ';
            $user = User::find($user_id);
            $data = ['link' => $link,
                'text' => $text,
                ];
            UserNotifications::create(
                [
                    'user_id' => $user_id,
                    'link' => $link,
                    'title' => $title_notification,
                    'text' => $text,
                    'is_read' => 0,
                ]
            );
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);

            $notificationBuilder = new PayloadNotificationBuilder($title);
            $notificationBuilder->setBody($text)
                ->setSound('default');

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData(['a_data' => $data]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $token = $user->device_token;

            if ($token) {
                $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();
                // return Array - you must remove all this tokens in your database
                $downstreamResponse->tokensToDelete();
                // return Array (key : oldToken, value : new token - you must change the token in your database)
                $downstreamResponse->tokensToModify();
                // return Array - you should try to resend the message to the tokens in the array
                $downstreamResponse->tokensToRetry();
                // return Array (key:token, value:error) - in production you should remove from your database the tokens
                $downstreamResponse->tokensWithError();

            }
        }
    }
}
