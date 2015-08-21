<?php

namespace RevAPI;

use RevAPI\Exception\UnexpectedValueException;

class TranslationOrderSubmission extends AbstractOrderSubmission
{
    protected $source_language_code;
    
    protected $destination_language_code;
    
    public function __construct(Rev $rev, $source_language_code, $destination_language_code)
    {
        parent::__construct($rev);
        
        $this->source_language_code      = $source_language_code;
        $this->destination_language_code = $destination_language_code;
    }

    /**
     * Add an input
     *
     * @param AbstractInput $input
     * @throws UnexpectedValueException
     */
    function addInput(AbstractInput $input)
    {
        if (!$input instanceof DocumentInput) {
            throw new UnexpectedValueException('The input must be a DocumentInput');
        }

        /**
         * @var $input DocumentInput
         */

        $data = array(
            'uri' => $input->getURI(),
            'word_length' => $input->getWordLength()
        );

        $this->inputs[] = $data;
    }
    
    /**
     * @return array
     */
    public function generatePostData()
    {
        $data = $this->generateBasePostData();

        $data['transcription_options'] = array();
        $data['transcription_options']['inputs'] = $this->generatePostDataForInputs();

        $data['transcription_options']['source_language_code'] = $this->source_language_code;
        $data['transcription_options']['destination_language_code'] = $this->destination_language_code;

        return $data;
    }

    public function send()
    {
        return $this->rev->sendTranslationOrder($this);
    }
}
