<?php

namespace RevAPI;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class Rev {
    const PRODUCTION_HOST = 'www.rev.com';
    const SANDBOX_HOST    = 'api-sandbox.rev.com';

    /**
     * @var string
     */
    protected $base_api_url;

    /**
     * @var HttpClient
     */
    protected $http_client;

    /**
     * @param string $client_api_key
     * @param string $user_api_key
     * @param string|null $host - if null, it will use the default production host
     * @param array $http_config - array of guzzle http configuration options
     */
    public function __construct($client_api_key, $user_api_key, $host = null, $http_config = array())
    {
        if (null == $host) {
            $host = self::PRODUCTION_HOST;
        }

        $this->base_api_url = 'https://' . $host . '/api/v1/';
        
        $http_config['headers']['Authorization'] = 'Rev ' . $client_api_key .':' . $user_api_key;
        $http_config['headers']['Content-Type'] = 'application/json';
        $http_config['base_uri'] = $this->base_api_url;
        
        
        $this->http_client = new HttpClient($http_config);
    }

    /**
     * Send a request and convert any BadResponseExceptions to a RequestException
     * 
     * @param \GuzzleHttp\Psr7\Request $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws Exception\RequestException
     */
    protected function sendRequest(Request $request)
    {
        try {
            return $this->http_client->send($request);
        } catch (ClientException $e) {
            throw new Exception\RequestException($e);
        }
    }

    /**
     * Get all orders
     *
     * @param int $page - the number of the page of orders to return. Optional, by default returns the first page
     * @param int $page_size - the number of orders per page to return. Optional, by default returns 25 orders per page. Page size has to be between 5 and 100
     * @return Orders
     * @throws Exception\RequestException
     */
    public function getOrders($page = 0, $page_size = 25)
    {
        $request = new Request('GET', 'orders?page=' . (int)$page . '&page_size=' . (int)$page_size);

        $response = $this->sendRequest($request);
        
        return new Orders($this, json_decode($response->getBody(), true));
    }

    /**
     * @param $order_number
     * @return Order
     * @throws Exception\RequestException
     */
    public function getOrder($order_number)
    {
        $request = new Request('GET', 'orders/' . $order_number);
        
        $response = $this->sendRequest($request);

        return new Order($this, json_decode($response->getBody(), true));
    }

    /**
     * Get the order number from an order URL
     * 
     * @param string $order_url - the absolute URL for the order as returned by the API while submitting an order
     * @return string
     */
    protected function getOrderNumber($order_url)
    {
        return str_replace($this->base_api_url . 'orders/', '', $order_url);
    }

    /**
     * @param string $url - the absolute URL to the video
     * @param string|null $content_type
     * @param int|null $video_length_seconds
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
        
        $request = new Request('POST', 'inputs', array(), $data);

        try {
            $rev_uri = $this->sendRequest($request)->getHeader('Location')[0];
            return new VideoInput($rev_uri, $video_length_seconds);
        } catch (ClientException $e) {
            throw new Exception\RequestException($e);
        }
    }

    /**
     * @param string $url - the absolute URL to the document
     * @param int $word_length
     * @param string|null $content_type
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
        
        $request = new Request('POST', 'inputs', array(), $data);

        try {
            $rev_uri = (string)$this->sendRequest($request)->getHeader('Location')[0];
            return new DocumentInput($rev_uri, $word_length);
        } catch (ClientException $e) {
            throw new Exception\RequestException($e);
        }
    }

    /**
     * @param CaptionOrderSubmission $order
     * @return string - the order number for the new order
     * @throws Exception\RequestException
     */
    public function sendCaptionOrder(CaptionOrderSubmission $order)
    {
        $data = $order->generatePostData();
        $data = json_encode($data);
        $request = new Request('POST', 'orders', array(), $data);

        $order_url = $this->sendRequest($request)->getHeader('Location')[0];
        return $this->getOrderNumber($order_url);
    }

    /**
     * @param TranscriptionOrderSubmission $order
     * @return string - the order number for the new order
     * @throws Exception\RequestException
     */
    public function sendTranscriptionOrder(TranscriptionOrderSubmission $order)
    {
        $data = $order->generatePostData();
        $data = json_encode($data);
        $request = new Request('POST', 'orders', array(), $data);

        $order_url = (string)$this->sendRequest($request)->getHeader('Location')[0];
        return $this->getOrderNumber($order_url);
    }
    
    /**
     * @param TranslationOrderSubmission $order
     * @return string - the order number for the new order
     * @throws Exception\RequestException
     */
    public function sendTranslationOrder(TranslationOrderSubmission $order)
    {
        $data = $order->generatePostData();
        $data = json_encode($data);
        $request = new Request('POST', 'orders', array(), $data);

        $order_url = (string)$this->sendRequest($request)->getHeader('Location')[0];
        return $this->getOrderNumber($order_url);
    }

    /**
     * @param string $attachment_id
     * @return Attachment The body of the attachment
     * @throws Exception\RequestException
     */
    public function getAttachment($attachment_id)
    {
        $request = new Request('GET', 'attachments/' . $attachment_id);
        $response = $this->sendRequest($request);
        return new Attachment($this, json_decode($response->getBody(), true));
    }

    /**
     * @param string $attachment_id
     * @param string|null $extension - the extension of the file, defaults to the original file extension. Ex. '.txt'
     * @return false|string The body of the attachment
     * @throws Exception\RequestException
     */
    public function getAttachmentContent($attachment_id, $extension = null)
    {
        $request = new Request('GET', 'attachments/' . $attachment_id . '/content' . $extension);
        
        return (string)$this->sendRequest($request)->getBody();
    }

    /**
     * Cancel an order
     * 
     * @param string $order_num
     * @return bool - true on success, exception or false on error
     * @throws Exception\RequestException
     */
    public function cancelOrder($order_num)
    {
        $request = new Request('POST', 'orders/' . $order_num . '/cancel');
        
        $result = $this->sendRequest($request);
        
        if (204 == $result->getStatusCode()) {
            return true;
        }
        
        return false;
    }
}
