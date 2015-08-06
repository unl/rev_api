<?php

namespace RevAPI;

class RevIntegrationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetOrders()
    {
        $rev = $this->getClient();
        $orders = $rev->getOrders();
        
        $this->assertArrayHasKey('orders', $orders);
    }
    
    protected function getClient()
    {
        return new Rev(REV_CLIENT_API_KEY, REV_USER_API_KEY, Rev::SANDBOX_HOST);
    }
    
}