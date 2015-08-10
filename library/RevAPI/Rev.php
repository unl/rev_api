<?php

namespace RevAPI;

use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\Exception\BadResponseException;

class Rev {
    
    const PRODUCTION_HOST = 'www.rev.com';
    const SANDBOX_HOST    = 'api-sandbox.rev.com';

    /**
     * @var HttpClient
     */
    protected $http_client;
    
    public function __construct($client_api_key, $user_api_key, $host = null, $http_config = array())
    {
        if (null == $host) {
            $host = self::PRODUCTION_HOST;
        }
        
        $http_config['request.options']['headers']['Authorization'] = 'Rev ' . $client_api_key .':' . $user_api_key;
        $http_config['request.options']['headers']['Content-Type'] = 'application/json';
        
        $this->http_client = new HttpClient('https://' . $host . '/api/v1/', $http_config);
    }
    
    public function getOrders()
    {
        $request = $this->http_client->get('orders');

        return $request->send()->json();
    }
    
    public function uploadUrl($url, $content_type = null)
    {
        $data = array();
        $data['url'] = $url;
        
        if ($content_type) {
            $data['content_type'] = $content_type;
        }
        
        $data = json_encode($data);
        
        $request = $this->http_client->post('inputs', null, $data);
        
        return (string)$request->send()->getHeader('Location');
    }
    
    public function sendCaptionOrder(AbstractOrderSubmission $order)
    {
        $data = $order->generatePostData();
        $data = json_encode($data);
        $request = $this->http_client->post('orders', null, $data);

        try {
            return (string)$request->send()->getHeader('Location');
        } catch (BadResponseException $e) {
            print_r((string)$e->getResponse()->getBody());
        }
    }
}
