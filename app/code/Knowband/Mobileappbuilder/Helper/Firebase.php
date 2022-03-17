<?php

/**
 * Knowband_Mobileappbuilder
 *
 * @category    Knowband
 * @package     Knowband_Mobileappbuilder
 * @author      Knowband Team <support@knowband.com.com>
 * @copyright   Knowband (http://wwww.knowband.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Knowband\Mobileappbuilder\Helper;

class Firebase extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $sp_storeManager;
    protected $sp_scopeConfig;
    protected $sp_request;
    protected $sp_state;
    protected $inlineTranslation;
    protected $sp_transportBuilder;
    protected $rulesFactory;
    protected $sp_customerGroup;
    protected $sp_objectManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    )
    {
        parent::__construct($context);
    }
    
    // sending push message to single user by firebase reg id
    public function send($to, $message, $server_key, $device = 'both') {
        if ($device == 'android') {
            $fields = array(
                'to' => $to,
                'data' => $message,
                'priority' => "high",
                'mutable_content' => true,
                'content_available' => true
            );
            return $this->sendPushNotification($fields, $server_key);
        } else if ($device == 'ios') {
            $fields = array(
                'to' => $to,
                'data' => $message,
                'priority' => "high",
                'mutable_content' => true,
                'content_available' => true,
                'notification' => array(
                    'title' => $message['data']['title'],
                    'body' => $message['data']['message']
                ),
            );
            return $this->sendPushNotification($fields, $server_key);
        } else {
            $fields = array(
                'to' => $to,
                'data' => $message,
                'priority' => "high",
                'mutable_content' => true,
                'content_available' => true,
                'notification' => array(
                    'title' => $message['data']['title'],
                    'body' => $message['data']['message']
                ),
            );
            return $this->sendPushNotification($fields, $server_key);
        }
    }

    // Sending message to a topic by topic name
    public function sendToTopic($to, $message, $server_key, $device = 'both') {
        if ($device == 'android') {
            $fields = array(
                'to' => '/topics/' . $to,
                'data' => $message,
                'priority' => "high",
                'mutable_content' => true,
                'content_available' => true
            );
        } elseif ($device == 'ios') {
            $fields = array(
                'to' => '/topics/' . $to,
                'data' => $message,
                'priority' => "high",
                'mutable_content' => true,
                'content_available' => true,
                'notification' => array(
                    'title' => $message['data']['title'],
                    'body' => $message['data']['message']
                ),
            );
        } else {
            $fields = array(
                'to' => '/topics/' . $to,
                'data' => $message,
                'priority' => "high",
                'mutable_content' => true,
                'content_available' => true,
                'notification' => array(
                    'title' => $message['data']['title'],
                    'body' => $message['data']['message']
                ),
            );
        }

        return $this->sendPushNotification($fields, $server_key);
    }

    public function sendMultiple($registration_ids, $message, $server_key, $device = 'both') {
        if ($device == 'android') {
            $fields = array(
                'to' => $registration_ids,
                'data' => $message,
                'priority' => "high",
                'mutable_content' => true,
                'content_available' => true
            );
        } else if ($device == 'ios') {
            $fields = array(
                'to' => $registration_ids,
                'data' => $message,
                'priority' => "high",
                'mutable_content' => true,
                'content_available' => true,
                'notification' => array(
                    'title' => $message['data']['title'],
                    'body' => $message['data']['message']
                ),
            );
        } else {
            $fields = array(
                'to' => $registration_ids,
                'data' => $message,
                'priority' => "high",
                'mutable_content' => true,
                'content_available' => true,
                'notification' => array(
                    'title' => $message['data']['title'],
                    'body' => $message['data']['message']
                ),
            );
        }

        return $this->sendPushNotification($fields, $server_key);
    }

    // function makes curl request to firebase servers
    private function sendPushNotification($fields, $server_key = '') {

        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            'Authorization: key=' . $server_key,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));


        // Execute post
        $result = curl_exec($ch);

        if ($result === FALSE) {
            return ('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        return $result;
    }

}
