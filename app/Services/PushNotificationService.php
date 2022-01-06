<?php


namespace App\Services;


use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CustomLog;

class PushNotificationService
{
    /**
     * Notification configuration.
     *
     * @var array
     */
    private $config;

    /**
     * Notification Url.
     *
     * @var string
     */
    private $apiUrl;

    private $status = 'sandbox';
    /**
     * NotificationService constructor.
     */
    public function __construct()
    {
        $this->config = config('notification-push');
        if ($this->config['mode'] === 'sandbox') {
            $this->apiUrl = 'https://fcm.googleapis.com/fcm/send';
            $this->status = 'sandbox';

        } else {
            $this->apiUrl = 'https://fcm.googleapis.com/fcm/send';
            $this->status = 'live';
        }
    }

    /**
     * @param $fields
     * @return bool
     */
    private function sendNotification($fields)
    {
        //dd($fields);
        $headers = array(
            'Authorization: key=' .$this->config[$this->status]['server_key'],
            'Content-Type: application/json'
        );
        //dd($headers);
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // return json_encode($fields);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);
        return true;
    }

    /**
     * @param $title
     * @param $body
     * @return bool
     */
    public function sendGeneralNotification($title, $body)
    {
        $msg = array('custom'=>array('custom_message' =>array
        (
            'message_data' => $body,
            'title'=> $title,
            'pushType' => "GENERAL"
        )));
        $newarray['message']=$msg;
        $fields = array(
            'to' => "/topics/".$this->config[$this->status]['topic_name_android'],
            'priority' => "high",
            'data' => $msg,
        );

        CustomLog::info('General Notification request: <br>'.json_encode($fields));
        // Open connection
        return $this->sendNotification($fields);
    }

    /**
     * @param $title
     * @param $body
     * @return bool
     */
    public function sendGeneralNotificationForRider($title, $body)
    {
        $msg = array('custom'=>array('custom_message' =>array
        (
            'message_data' => $body,
            'title'=> $title,
            'pushType' => "GENERAL"
        )));
        $newarray['message']=$msg;
        $fields = array(
            'to' => "/topics/".$this->config[$this->status]['topic_name_android_rider'],
            'priority' => "high",
            'data' => $msg,
        );

        CustomLog::info('General Notification request: <br>'.json_encode($fields));
        // Open connection
        return $this->sendNotification($fields);
    }

    /**
     * @param $title
     * @param $body
     * @return bool
     */
    public function sendGeneralNotificationForDriver($title, $body)
    {
        $msg = array('custom'=>array('custom_message' =>array
        (
            'message_data' => $body,
            'title'=> $title,
            'pushType' => "GENERAL"
        )));
        $newarray['message']=$msg;
        $fields = array(
            'to' => "/topics/".$this->config[$this->status]['topic_name_android_driver'],
            'priority' => "high",
            'data' => $msg,
        );

        CustomLog::info('General Notification request: <br>'.json_encode($fields));
        // Open connection
        return $this->sendNotification($fields);
    }

    /**
     * @param $title
     * @param $body
     * @return bool
     */
    public function sendGeneralNotificationForIOS($title, $body)
    {
        $msg = array('custom'=>array('custom_message' =>array
        (
            'message_data' => $body,
            'title'=> $title,
            'sound'=> 'notificationCupcake.caf',
            'pushType' => "GENERAL"
        )));
        $newarray['message']=$msg;
        $fields = array(
            'to' => "/topics/".$this->config[$this->status]['topic_name_ios'],
            'priority' => "high",
            'mutable_content' => true,
            'notification' => $msg,
        );

        CustomLog::info('General Notification request: <br>'.json_encode($fields));
        // Open connection
        return $this->sendNotification($fields);
    }

    /**
     * @param $title
     * @param $body
     * @return bool
     */
    public function sendGeneralNotificationForIOSForRider($title, $body)
    {
        $msg = array('custom'=>array('custom_message' =>array
        (
            'message_data' => $body,
            'title'=> $title,
            'sound'=> 'notificationCupcake.caf',
            'pushType' => "GENERAL"
        )));
        $newarray['message']=$msg;
        $fields = array(
            'to' => "/topics/".$this->config[$this->status]['topic_name_ios_rider'],
            'priority' => "high",
            'mutable_content' => true,
            'notification' => $msg,
        );

        CustomLog::info('General Notification request: <br>'.json_encode($fields));
        // Open connection
        return $this->sendNotification($fields);
    }

    /**
     * @param $title
     * @param $body
     * @return bool
     */
    public function sendGeneralNotificationForIOSForDriver($title, $body)
    {
        $msg = array('custom'=>array('custom_message' =>array
        (
            'message_data' => $body,
            'title'=> $title,
            'sound'=> 'notificationCupcake.caf',
            'pushType' => "GENERAL"
        )));
        $newarray['message']=$msg;
        $fields = array(
            'to' => "/topics/".$this->config[$this->status]['topic_name_ios_driver'],
            'priority' => "high",
            'mutable_content' => true,
            'notification' => $msg,
        );

        CustomLog::info('General Notification request: <br>'.json_encode($fields));
        // Open connection
        return $this->sendNotification($fields);
    }

    /**
     * @param $gcm_id
     * @param $title
     * @param $body
     * @return bool
     */
    public function sendSpecificNotification($gcm_id, $title, $body)
    {
        $msg = array('custom'=>array('custom_message' =>array(
            'message_data' => $body,
            'title'=> $title,
            'pushType' => "GENERAL"
        )));
        $newarray['message']=$msg;
        $fields = array(
            'registration_ids' => explode(',',$gcm_id),
            'priority' => "high",
            'data' => $msg,
        );
        //dd($fields);
        CustomLog::info('Specific Notification request: <br>'.json_encode($fields));

        // Open connection
        return $this->sendNotification($fields);
    }

    /**
     * @param $gcm_id
     * @param $title
     * @param $body
     * @return bool
     */
    public function sendSpecificNotificationForIOS($gcm_id, $title, $body)
    {
        $msg = array('custom'=>array('custom_message' =>array
        (
            'message_data' => $body,
            'title'=> $title,
            'sound'=> 'notificationCupcake.caf',
            'pushType' => "GENERAL"
        )));
        $newarray['message']=$msg;
        $fields = array(
            'registration_ids' => explode(',',$gcm_id),
            'priority' => "high",
            'mutable_content' => true,
            'notification' => $msg,
        );
        CustomLog::info('Specific Notification request: <br>'.json_encode($fields));

        // Open connection
        return $this->sendNotification($fields);
    }

    /**
     * @param $gcm_id
     * @param $title
     * @param $body
     * @return bool
     */
    public function sendAutoNotification($gcm_id, $title, $body)
    {
        $msg = array('custom'=>array('custom_message' =>array
        (
            'message_data' => $body,
            'title'=> $title,
            'pushType' => "GENERAL"
        )));
        $newarray['message']=$msg;
        $fields = array(
            'to' => $gcm_id,
            'priority' => "high",
            'data' => $msg,
        );
        CustomLog::info('Auto Notification request: <br>'.json_encode($fields));
        // Open connection
        return $this->sendNotification($fields);
    }

    /**
     * @param $gcm_id
     * @param $title
     * @param $body
     * @return bool
     */
    public function sendAutoNotificationForIOS($gcm_id, $title, $body)
    {
        $msg = array('custom'=>array('custom_message' =>array
        (
            'message_data' => $body,
            'sound' => 'notificationCupcake.caf',
            'title'=> $title,
            'pushType' => "GENERAL"
        )));
        $newarray['message']=$msg;
        $fields = array(
            'to' => $gcm_id,
            'priority' => "high",
            'mutable_content' => true,
            'notification' => $msg,
        );
        CustomLog::info('Auto Notification request: <br>'.json_encode($fields));
        // Open connection
        return $this->sendNotification($fields);
    }

    /**
     * @param $input
     * @return mixed
     */
    public function tripPushNotificationMessage($input)
    {
        $pushMessage = StaticPushNotificationMessageService::$TripLifeCycle[$input['message_index']];
        $title = sprintf(
            $pushMessage['title'],
            ($input['payment_gateway'] ?? 'Nagad'), ($input['ride_share_company_name'] ?? config('app.name')),
            ($input['discount_rate'] ?? 0), ($input['amount'] ?? 0), ($input['number_of_trip'] ?? 0),
            ($input['number_of_day_or_date'] ?? null), ($input['driver_name'] ?? 'Driver'),
            ($input['driver_total_rating'] ?? null), ($input['pick_up_time'] ?? null), ($input['pin_number'] ?? null),
            ($input['car_type_name'] ?? null), ($input['car_reg_number'] ?? null), ($input['rider_name'] ?? 'Rider')
        );
        $body = sprintf(
            $pushMessage['message'],
            ($input['payment_gateway'] ?? 'Nagad'), ($input['ride_share_company_name'] ?? config('app.name')),
            ($input['discount_rate'] ?? 0), ($input['amount'] ?? 0), ($input['number_of_trip'] ?? 0),
            ($input['number_of_day_or_date'] ?? null), ($input['driver_name'] ?? 'Driver'),
            ($input['driver_total_rating'] ?? null), ($input['pick_up_time'] ?? null), ($input['pin_number'] ?? null),
            ($input['car_type_name'] ?? null), ($input['car_reg_number'] ?? null), ($input['rider_name'] ?? 'Rider')
        );
        if($input['device_type'] == 2):
            $return = $this->sendAutoNotification($input['device_id'], $title, $body);
        else:
            $return = $this->sendAutoNotificationForIOS($input['device_id'], $title, $body);
        endif;
        return $return;
    }
}
