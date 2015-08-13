<?php

namespace RevAPI;

class Orders extends \ArrayIterator
{
    /**
     * @var Rev
     */
    protected $rev;
    
    protected $data;
    
    public function __construct(Rev $rev, $orders_array) {
        $this->rev = $rev;
        $this->data = $orders_array;
        parent::__construct($orders_array['orders']);
    }

    /**
     * @return Order
     */
    public function current()
    {
        $current = parent::current();
        
        //Grab the order with the API call to get attachments and comments
        return $this->rev->getOrder($current['order_number']);
    }
    
    public function getOrdersData()
    {
        return $this->data;
    }
}
