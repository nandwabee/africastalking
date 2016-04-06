<?php namespace AfricasTalking;

use AfricasTalking\Foundation\SmsIntegration;
use GuzzleHttp\Client;

/**
 * Core class for the AfricasTalking API tools.
 *
 * @author Bernard Nandwa <nandwabee@gmail.com>
 * @package nandwabee\africastalking
 */
 
class AfricasTalking{
    /**
     * AfricasTalking constructor.
     *
     * Instantiate the credentials array and the Guzzle client.
     *
     * @param $username string
     * @param $key string
     */
    function __construct($username,$key){
        $this->client = new Client(
            [
                'headers' => ['Accept' => 'application/json','apikey' => $key]
            ]
        );

        $this->credentials = [
            'username' => $username,
            'key' => $key
        ];
    }

    /**
     * Send an sms message
     *
     * @param $recipients array
     * @param $message string The message to be sent
     * @param $options array
     *
     * @todo Format the response
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function send(array $recipients,$message,array $options){
        $obj = new SmsIntegration($this->credentials,$this->client);

        $to = implode(",",$recipients);

        $response = $obj->send($to,$message,$options);

        //Need to format the response
        return $response;
    }

    /**
     * Get the messages that you have sent.
     *
     * @param $last_id
     * @return mixed
     */
    public function get_messages($last_id = 0){
        $obj = new SmsIntegration($this->credentials,$this->client);

        return $obj->get_messages($last_id);
    }

    /**
     * Handle the delivery reports from AfricasTalking. The data is obtained simply from the POST parameters.
     *
     * We return a simple array.
     *
     * @return array
     */
    public function handle_delivery_report(){
        $report = [];

        $report['status'] = $_POST['status'];
        $report['message_id'] = $_POST['id'];
        $report['reason'] = (isset($_POST['failureReason'])) ? $_POST['failureReason'] : null;

        return $report;
    }
    
    /**
     * Get the details of the user.
     *
     * @return array
     */
    public function get_user(){
        $request = $this->client->createRequest('GET', 'https://api.africastalking.com/version1/user');
        
        $request->addHeader('apikey',$this->credentials['key']);
        $request->addHeader('Accept','application/json');
        
        $params = $request->getQuery();

        $params->set('username',$this->credentials['username']);
        
        $response = $this->client->send($request);

        return $response->json()['balance'];
    }
}