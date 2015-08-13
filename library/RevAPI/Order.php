<?php

namespace RevAPI;

class Order
{
    /**
     * @var Rev
     */
    protected $rev;

    /**
     * @var \stdClass
     */
    protected $order_data;

    /**
     * The status for complete orders
     */
    const ORDER_STATUS_COMPLETE = 'Complete';
    
    public function __construct(Rev $rev, $order_data)
    {
        $this->rev = $rev;
        $this->order_data = $order_data;
    }

    /**
     * Get the order number
     * 
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->order_data['order_number'];
    }

    /**
     * Get the client reference of this order
     * 
     * @return string
     */
    public function getClientReference()
    {
        return $this->order_data['client_ref'];
    }

    /**
     * Get the status of this order
     * 
     * @return string
     */
    public function getStatus()
    {
        return $this->order_data['status'];
    }

    /**
     * Get attachments for this order
     * 
     * @return Attachments
     */
    public function getAttachments()
    {
        return new Attachments($this->rev, $this->order_data['attachments']);
    }

    /**
     * Get the raw order data
     * 
     * @return \stdClass
     */
    public function getOrderData()
    {
        return $this->order_data;
    }

    /**
     * Determine if this order is complete
     * 
     * @return bool
     */
    public function isComplete()
    {
        return $this->getStatus() == self::ORDER_STATUS_COMPLETE;
    }
}
