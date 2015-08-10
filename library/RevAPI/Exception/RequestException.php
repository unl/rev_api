<?php

namespace RevAPI\Exception;
use Guzzle\Http\Exception\BadResponseException;

class RequestException extends \Exception {
    
    function __construct(BadResponseException $badResponseException)
    {
        $message  = $badResponseException->message;
        $response = $badResponseException->getResponse()->getBody();
        $response = json_decode($response);
        
        //Add rev data to the message for easy debugging
        if ($response && is_object($response)) {
            $message .= PHP_EOL;
            foreach (get_object_vars($response) as $key=>$value) {
                $message .= '[rev ' . $key . '] ' . $value . PHP_EOL; 
            }
        }
        
        parent::__construct($message, $badResponseException->code, $badResponseException);
    }
}
