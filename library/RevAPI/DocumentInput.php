<?php

namespace RevAPI;

class DocumentInput extends AbstractInput {

    protected $video_length_seconds = null;

    public function __construct($rev_uri, $word_length)
    {
        $this->uri = $rev_uri;
        $this->word_length = $word_length;
    }
    
    public function getWordLength()
    {
        return $this->word_length;
    }
}
