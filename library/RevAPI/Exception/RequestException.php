<?php

namespace RevAPI\Exception;
use Guzzle\Http\Exception\BadResponseException;
use RevAPI\Exception;

class RequestException extends Exception {
    
    protected $rev_message;
    
    protected $rev_code;
    
    function __construct(BadResponseException $badResponseException)
    {
        $message  = $badResponseException->message;
        $response = $badResponseException->getResponse()->getBody();
        $response = json_decode($response);
        
        //Add rev data to the message for easy debugging
        if ($response && is_object($response)) {
            
            if (isset($response->code, $response->message)) {
                $this->rev_code = $response->code;
                $this->rev_message = $response->message;
            }
            
            $message .= PHP_EOL;
            foreach (get_object_vars($response) as $key=>$value) {
                $message .= '[rev ' . $key . '] ' . $value . PHP_EOL; 
            }
        }
        
        parent::__construct($message, $badResponseException->code, $badResponseException);
    }
    
    public function getRevCode()
    {
        return $this->rev_code;
    }
    
    public function getRevMessage()
    {
        return $this->rev_message;
    }
}
