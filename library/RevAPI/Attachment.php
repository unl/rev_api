<?php

namespace RevAPI;

class Attachment
{
    protected $rev;

    protected $attachment_data;

    public function __construct(Rev $rev, $attachment_data)
    {
        $this->rev = $rev;
        $this->attachment_data = $attachment_data;
    }

    public function getKind()
    {
        return $this->attachment_data['kind'];
    }
    
    public function getName()
    {
        return $this->attachment_data['name'];
    }
    
    public function getId()
    {
        return $this->attachment_data['id'];
    }
    
    public function getAttachmentData()
    {
        return $this->attachment_data;
    }
}
