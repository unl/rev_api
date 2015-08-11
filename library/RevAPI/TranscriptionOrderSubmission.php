<?php

namespace RevAPI;

use RevAPI\Exception\UnexpectedValueException;

class TranscriptionOrderSubmission extends AbstractVideoOrderSubmission
{
    protected $verbatim = false;
    
    protected $timestamps = false;

    /**
     * Transcribe the provided files verbatim (include filler words (i.e. umm, huh)).
     */
    public function transcribeVerbatim()
    {
        $this->verbatim = true;
    }

    /**
     * Do not transcribe provided files verbatim
     */
    public function doNotTranscribeVerbatim()
    {
        $this->verbatim = false;
    }

    /**
     * Include timestamps in output
     */
    public function includeTimestamps()
    {
        $this->timestamps = true;
    }

    /**
     * Do not include timestamps in output.
     */
    public function doNotIncludeTimestamps()
    {
        $this->timestamps = false;
    }

    /**
     * @return \stdClass
     */
    public function generatePostData()
    {
        $data = $this->generateBasePostData();

        $data->transcription_options = new \stdClass();
        $data->transcription_options->inputs = $this->generatePostDataForInputs();

        $data->transcription_options->verbatim = $this->verbatim;
        $data->transcription_options->timestamps = $this->timestamps;

        return $data;
    }

    public function send()
    {
        return $this->rev->sendTranscriptionOrder($this);
    }
}