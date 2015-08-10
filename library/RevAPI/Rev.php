<?php

namespace RevAPI;

use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Message\RequestInterface;

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

    /**
     * Send a request and convert any BadResponseExceptions to a RequestException
     * 
     * @param RequestInterface $request
     * @return \Guzzle\Http\Message\Response
     * @throws Exception\RequestException
     */
    protected function sendRequest(RequestInterface $request)
    {
        try {
            return $request->send();
        } catch (BadResponseException $e) {
            throw new Exception\RequestException($e);
        }
    }

    /**
     * Get all orders
     * 
     * @return array|bool|float|int|string
     * @throws Exception\RequestException
     */
    public function getOrders()
    {
        $request = $this->http_client->get('orders');

        return $this->sendRequest($request)->json();
    }

    /**
     * @param $url
     * @param null $content_type
     * @return string
     * @throws Exception\RequestException
     */
    public function uploadUrl($url, $content_type = null)
    {
        $data = array();
        $data['url'] = $url;
        
        if ($content_type) {
            $data['content_type'] = $content_type;
        }
        
        $data = json_encode($data);
        
        $request = $this->http_client->post('inputs', null, $data);

        try {
            return (string)$this->sendRequest($request)->getHeader('Location');
        } catch (BadResponseException $e) {
            throw new Exception\RequestException($e);
        }
    }

    /**
     * @param AbstractOrderSubmission $order
     * @return string
     * @throws Exception\RequestException
     */
    public function sendCaptionOrder(AbstractOrderSubmission $order)
    {
        $data = $order->generatePostData();
        $data = json_encode($data);
        $request = $this->http_client->post('orders', null, $data);

        return (string)$this->sendRequest($request)->getHeader('Location');
    }
}
