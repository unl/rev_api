<?php

namespace RevAPI;

abstract class AbstractOrderSubmission {
    
    /**
     * @var Rev
     */
    protected $rev;
    
    protected $client_ref = null;
    
    protected $comment = null;
    
    protected $priority = 'Normal';
    
    protected $inputs = array();

    protected $notification = array();
    
    const PRIORITY_TIME_INSENSITIVE = 'TimeInsensitive';
    const PRIORITY_NORMAL = 'Normal';
    
    const NOTIFICATION_LEVEL_DETAILED = 'Detailed';
    const NOTIFICATION_LEVEL_FINAL_ONLY = 'FinalOnly';

    /**
     * @param Rev $rev
     */
    public function __construct(Rev $rev)
    {
        $this->rev = $rev;
    }

    /**
     * Add an input
     * 
     * @param string $rev_input_uri the uri returned by a POST to the INPUT endpoint
     * @param int|null $video_length_seconds
     */
    public function addInput($rev_input_uri, $video_length_seconds = null)
    {
        $input = array(
            'uri' => $rev_input_uri
        );
        
        if (null !== $video_length_seconds) {
            $input['video_length_seconds'] = $video_length_seconds;
        }

        $this->inputs[] = $input;
    }

    /**
     * @param string $url the absolute URL which REV will post to for notifications
     * @param string|null $level The notification level, either OrderSubmission::NOTIFICATION_LEVEL_DETAILED or OrderSubmission::NOTIFICATION_LEVEL_FINAL_ONLY
     * @throws Exception
     */
    public function setNotification($url, $level = null)
    {
        if ($level) {
            if (!in_array($level, array(
                self::NOTIFICATION_LEVEL_DETAILED,
                self::NOTIFICATION_LEVEL_FINAL_ONLY
            ))) {
                throw new Exception('A valid level must be selected');
            }

            $this->notification['level'] = $level;
        }
        
        $this->notification['url'] = $url;
    }

    /**
     * Set the priority of the order
     * 
     * @param string $priority either OrderSubmission::PRIORITY_NORMAL or OrderSubmission::PRIORITY_TIME_INSENSITIVE
     * @throws Exception
     */
    public function setPriority($priority)
    {
        if (!in_array($priority, array(
            self::PRIORITY_NORMAL,
            self::PRIORITY_TIME_INSENSITIVE
        ))) {
            throw new Exception('A valid priority must be selected');
        }
        
        $this->priority = $priority;
    }

    /**
     * Set the comment with any special messages about the order
     * 
     * @param $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Set the client reference number for the order meaningful for the client
     * 
     * @param string $client_ref
     */
    public function setClientRef($client_ref)
    {
        $this->client_ref = $client_ref;
    }
    
    /**
     * All of the different kinds of order share the same structure for inputs. 
     * This method generates that structure for use in the POST request
     * 
     * @return array
     */
    protected function generatePostDataForInputs()
    {
        $data = array();
        
        foreach ($this->inputs as $input) {
            $data[] = (object)$input;
        }
        
        return $data;
    }

    /**
     * All of the different kinds of orders have a similar set of attributes.
     * This function generates an object with those attributes for use in the POST request
     * 
     * @return \stdClass - The base data describing the order
     */
    public function generateBasePostData()
    {
        $data = new \stdClass();

        $data->priority = $this->priority;

        if ($this->client_ref) {
            $data->client_ref = $this->client_ref;
        }

        if ($this->comment) {
            $data->comment = $this->comment;
        }

        if (!empty($this->notification)) {
            $data->notification = (object)$this->notification;
        }

        return $data;
    }

    /**
     * Generate the final post data array
     * 
     * @return \stdClass - The final object describing the order
     */
    abstract function generatePostData();

    /**
     * Send the order.
     * 
     * @return string - Will return the URI for the order on success
     */
    abstract function send();
}
