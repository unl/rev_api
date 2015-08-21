<?php

namespace RevAPI;

abstract class AbstractInput {
    
    protected $uri;
    
    public function getURI()
    {
        return $this->uri;
    }
}
