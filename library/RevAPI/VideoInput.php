<?php

namespace RevAPI;

class VideoInput extends AbstractInput {
    
    protected $video_length_seconds = null;
    
    public function __construct($rev_uri, $video_length_seconds = null)
    {
        $this->uri = $rev_uri;
        $this->video_length_seconds = $video_length_seconds;
    }
    
    public function getVideoLength() {
        return $this->video_length_seconds;
    }
}