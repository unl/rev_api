<?php

namespace RevAPI;

class Attachment
{
    const KIND_MEDIA = 'media';
    
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

    /**
     * @param string|null $extension - the extension of the file, defaults to the original file extension. Ex. '.txt'
     * @return false|string
     */
    public function getContent($extension = null)
    {
        return $this->rev->getAttachmentContent($this->getId(), $extension);
    }
    
    public function isMedia()
    {
        return $this->getKind() == self::KIND_MEDIA;
    }
}
