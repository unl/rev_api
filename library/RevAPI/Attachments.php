<?php

namespace RevAPI;

class Attachments extends \ArrayIterator
{
    /**
     * @var Rev
     */
    protected $rev;

    protected $data;

    public function __construct(Rev $rev, $attachments_array) {
        $this->rev = $rev;
        $this->data = $attachments_array;
        parent::__construct($attachments_array);
    }

    /**
     * @return Attachment
     */
    public function current()
    {
        $current = parent::current();
        return new Attachment($this->rev, $current);
    }

    public function getAttachmentsData()
    {
        return $this->data;
    }
}
