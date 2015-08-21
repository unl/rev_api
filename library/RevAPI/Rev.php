<?php

namespace RevAPI;

use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Message\RequestInterface;

class Rev {
    const PRODUCTION_HOST = 'www.rev.com';
    const SANDBOX_HOST    = 'api-sandbox.rev.com';
    
    protected $base_api_url;

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
        
        $this->base_api_url = 'https://' . $host . '/api/v1/';
        
        $this->http_client = new HttpClient($this->base_api_url, $http_config);
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
     * @param int $page - the number of the page of orders to return. Optional, by default returns the first page
     * @param int $page_size - the number of orders per page to return. Optional, by default returns 25 orders per page. Page size has to be between 5 and 100
     * @return Orders
     * @throws Exception\RequestException
     */
    public function getOrders($page = 0, $page_size = 25)
    {
        $request = $this->http_client->get('orders?page=' . (int)$page . '&page_size=' . (int)$page_size);

        return new Orders($this, $this->sendRequest($request)->json());
    }

    /**
     * @param $order_number
     * @return array|false The json_decoded result 
     * @throws Exception\RequestException
     */
    public function getOrder($order_number)
    {
        $request = $this->http_client->get('orders/' . $order_number);

        return new Order($this, $this->sendRequest($request)->json());
    }

    /**
     * Get the order number from an order URL
     * 
     * @param string $order_url - the absolute URL for the order as returned by the API while submitting an order
     * @return mixed
     */
    protected function getOrderNumber($order_url)
    {
        return str_replace($this->base_api_url . 'orders/', '', $order_url);
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

        $order_url = (string)$this->sendRequest($request)->getHeader('Location');
        return $this->getOrderNumber($order_url);
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

        $order_url = (string)$this->sendRequest($request)->getHeader('Location');
        return $this->getOrderNumber($order_url);
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

        $order_url = (string)$this->sendRequest($request)->getHeader('Location');
        return $this->getOrderNumber($order_url);
    }

    /**
     * @param $attachment_id
     * @return Attachment The body of the attachment
     * @throws Exception\RequestException
     */
    public function getAttachment($attachment_id)
    {
        $request = $this->http_client->get('attachments/' . $attachment_id);

        return new Attachment($this, $this->sendRequest($request)->json());
    }

    /**
     * @param $attachment_id
     * @param string|null $extension - the extension of the file, defaults to the original file extension. Ex. '.txt'
     * @return false|string The body of the attachment
     * @throws Exception\RequestException
     */
    public function getAttachmentContent($attachment_id, $extension = null)
    {
        $request = $this->http_client->get('attachments/' . $attachment_id . '/content' . $extension);
        
        return (string)$this->sendRequest($request)->getBody();
    }

    /**
     * Cancel an order
     * 
     * @param $order_num
     * @return bool - true on success, exception or false on error
     * @throws Exception\RequestException
     */
    public function cancelOrder($order_num)
    {
        $request = $this->http_client->post('orders/' . $order_num . '/cancel');
        
        $result = $this->sendRequest($request);
        
        if (204 == $result->getStatusCode()) {
            return true;
        }
        
        return false;
    }
}
