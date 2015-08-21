<?php

namespace RevAPI;

class Orders extends \ArrayIterator
{
    /**
     * @var Rev
     */
    protected $rev;
    
    protected $data;
    
    protected $orders;
    
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
        
        if (!isset($this->orders[$current['order_number']])) {
            //Grab the order with the API call to get attachments and comments
            //Also avoid calling multiple times while iterating
            $this->orders[$current['order_number']] = $this->rev->getOrder($current['order_number']);
        }
        
        return $this->orders[$current['order_number']];
    }
    
    public function getOrdersData()
    {
        return $this->data;
    }
    
    public function getResultsPerPage()
    {
        return $this->data['results_per_page'];
    }
    
    public function getTotalCount()
    {
        return $this->data['total_count'];
    }
    
    public function getCurrentPage()
    {
        return $this->data['page'];
    }
    
    public function getNextPage()
    {
        $results_per_page = $this->getResultsPerPage();
        $total_count = $this->getTotalCount();
        $current_page = $this->getCurrentPage();
        
        if (($current_page+1)*$results_per_page < $total_count) {
            return $this->rev->getOrders($current_page+1);
        }
        
        return false;
    }
    
    public function getPreviousPage()
    {
        $current_page = $this->getCurrentPage();

        if ($current_page-1 >= 0) {
            return $this->rev->getOrders($current_page-1);
        }

        return false;
    }
}
