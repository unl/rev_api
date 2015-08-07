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
    
    public function testUploadURL()
    {
        $rev = $this->getClient();
        $url = 'http://mediahub.unl.edu/uploads/a07d73f214fe6bacbd446e6b90be8aa9.mp4';
        
        $result = $rev->uploadUrl($url);
        
        $this->assertStringStartsWith('urn:rev:inputmedia:', $result);
    }
    
    protected function getClient()
    {
        return new Rev(REV_CLIENT_API_KEY, REV_USER_API_KEY, Rev::SANDBOX_HOST);
    }
    
}