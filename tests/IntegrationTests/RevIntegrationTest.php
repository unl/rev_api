<?php

namespace RevAPI;

class RevIntegrationTest extends \PHPUnit_Framework_TestCase
{
    const MEDIA_URL = 'http://mediahub.unl.edu/uploads/a07d73f214fe6bacbd446e6b90be8aa9.mp4';
    
    public function testGetOrders()
    {
        $rev = $this->getClient();
        $orders = $rev->getOrders();
        
        $this->assertArrayHasKey('orders', $orders);
    }
    
    public function testUploadURL()
    {
        $rev = $this->getClient();
        
        $result = $rev->uploadUrl(self::MEDIA_URL);
        
        $this->assertStringStartsWith('urn:rev:inputmedia:', $result);
    }
    
    public function testSendCaptionOrder()
    {
        $rev = $this->getClient();

        $uri = $rev->uploadUrl(self::MEDIA_URL);
        
        $order = new CaptionOrderSubmission($rev);
        
        $order->addInput($uri);
        
        $order->setClientRef('example reference number');
        $order->setComment('example comment');
        $order->setNotification('http://example.org/test.php', CaptionOrderSubmission::NOTIFICATION_LEVEL_DETAILED);
        $order->setPriority(CaptionOrderSubmission::PRIORITY_TIME_INSENSITIVE);
        $order->setOutputFormats(array('WebVtt', 'SubRip'));
        
        $result = $order->send();

        $this->assertStringStartsWith('http', $result);
    }
    
    protected function getClient()
    {
        return new Rev(REV_CLIENT_API_KEY, REV_USER_API_KEY, Rev::SANDBOX_HOST);
    }
    
}