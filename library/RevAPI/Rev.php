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
     * @param null $video_length_seconds
     * @return VideoInput
     * @throws Exception\RequestException
     */
    public function uploadVideoUrl($url, $content_type = null, $video_length_seconds = null)
    {
        $data = array();
        $data['url'] = $url;
        
        if ($content_type) {
            $data['content_type'] = $content_type;
        }
        
        $data = json_encode($data);
        
        $request = $this->http_client->post('inputs', null, $data);

        try {
            $rev_uri = (string)$this->sendRequest($request)->getHeader('Location');
            return new VideoInput($rev_uri, $video_length_seconds);
        } catch (BadResponseException $e) {
            throw new Exception\RequestException($e);
        }
    }

    /**
     * @param $url
     * @param $word_length
     * @param null $content_type
     * @return DocumentInput
     * @throws Exception\RequestException
     */
    public function uploadDocumentUrl($url, $word_length, $content_type = null)
    {
        $data = array();
        $data['url'] = $url;

        if ($content_type) {
            $data['content_type'] = $content_type;
        }

        $data = json_encode($data);

        $request = $this->http_client->post('inputs', null, $data);

        try {
            $rev_uri = (string)$this->sendRequest($request)->getHeader('Location');
            return new DocumentInput($rev_uri, $word_length);
        } catch (BadResponseException $e) {
            throw new Exception\RequestException($e);
        }
    }

    /**
     * @param CaptionOrderSubmission $order
     * @return string
     * @throws Exception\RequestException
     */
    public function sendCaptionOrder(CaptionOrderSubmission $order)
    {
        $data = $order->generatePostData();
        $data = json_encode($data);
        $request = $this->http_client->post('orders', null, $data);

        return (string)$this->sendRequest($request)->getHeader('Location');
    }

    /**
     * @param TranscriptionOrderSubmission $order
     * @return string
     * @throws Exception\RequestException
     */
    public function sendTranscriptionOrder(TranscriptionOrderSubmission $order)
    {
        $data = $order->generatePostData();
        $data = json_encode($data);
        $request = $this->http_client->post('orders', null, $data);

        return (string)$this->sendRequest($request)->getHeader('Location');
    }
    
    /**
     * @param TranslationOrderSubmission $order
     * @return string
     * @throws Exception\RequestException
     */
    public function sendTranslationOrder(TranslationOrderSubmission $order)
    {
        $data = $order->generatePostData();
        $data = json_encode($data);
        $request = $this->http_client->post('orders', null, $data);

        return (string)$this->sendRequest($request)->getHeader('Location');
    }
}
