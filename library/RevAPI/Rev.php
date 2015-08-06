<?php

namespace RevAPI;

use Guzzle\Http\Client as HttpClient;

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
        $http_config['request.options']['headers']['Content-Type'] = 'JSON';
        
        $this->http_client = new HttpClient('https://' . $host . '/api/v1/', $http_config);
    }
    
    public function getOrders()
    {
        $request = $this->http_client->get('orders');

        return $request->send()->json();
    }
}
