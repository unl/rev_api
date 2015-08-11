<?php

namespace RevAPI;

class CaptionOrderSubmission extends AbstractVideoOrderSubmission
{
    protected $output_file_formats = array(
        'WebVTT'
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
     * @return \stdClass
     */
    public function generatePostData()
    {
        $data = $this->generateBasePostData();
        
        $data->caption_options = new \stdClass();
        $data->caption_options->inputs = $this->generatePostDataForInputs();
        
        $data->caption_options->output_file_formats = $this->output_file_formats;
        
        return $data;
    }

    public function send()
    {
        return $this->rev->sendCaptionOrder($this);
    }
}