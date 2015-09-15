<?php

namespace RevAPI;

class CaptionOrderSubmission extends AbstractVideoOrderSubmission
{
    protected $output_file_formats = array(
        'WebVtt'
    );

    /**
     * Set the formats that this order will return
     *
     * @param array $formats an array of the formats
     */
    public function setOutputFormats(array $formats)
    {
        $this->output_file_formats = $formats;
    }

    /**
     * @return array
     */
    public function generatePostData()
    {
        $data = $this->generateBasePostData();
        
        $data['caption_options'] = array();
        $data['caption_options']['inputs'] = $this->generatePostDataForInputs();
        
        $data['caption_options']['output_file_formats'] = $this->output_file_formats;
        
        return $data;
    }

    /**
     * @return string - the order number for the new order
     */
    public function send()
    {
        return $this->rev->sendCaptionOrder($this);
    }
}
