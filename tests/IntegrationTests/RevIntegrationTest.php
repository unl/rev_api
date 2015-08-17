<?php

namespace RevAPI;

class RevIntegrationTest extends \PHPUnit_Framework_TestCase
{
    const MEDIA_URL = 'http://mediahub.unl.edu/uploads/a07d73f214fe6bacbd446e6b90be8aa9.mp4';
    
    public function testGetOrders()
    {
        $rev = $this->getClient();
        
        //first page of orders
        $orders = $rev->getOrders();
        
        $this->assertEquals(0, $orders->getCurrentPage());
        
        $previous_page = $orders->getCurrentPage();
        
        while ($orders = $orders->getNextPage()) {
            $this->assertGreaterThan($previous_page, $orders->getCurrentPage());
            $previous_page = $orders->getCurrentPage();
        }
        
        //Reset to the last page
        $orders = $rev->getOrders($previous_page);

        //Now, lets go backwards
        while ($orders = $orders->getPreviousPage()) {
            $this->assertLessThan($previous_page, $orders->getCurrentPage());
            $previous_page = $orders->getCurrentPage();
        }
    }
    
    public function testUploadVideoURL()
    {
        $rev = $this->getClient();
        
        $result = $rev->uploadVideoUrl(self::MEDIA_URL);
        
        $this->assertStringStartsWith('urn:rev:inputmedia:', $result->getURI());
    }
    
    public function testSendCaptionOrder()
    {
        $rev = $this->getClient();

        $input = $rev->uploadVideoUrl(self::MEDIA_URL);
        
        $order = new CaptionOrderSubmission($rev);
        
        $order->addInput($input);
        
        $order->setClientRef('example reference number');
        $order->setComment('example comment');
        $order->setNotification('http://example.org/test.php', CaptionOrderSubmission::NOTIFICATION_LEVEL_DETAILED);
        $order->setPriority(CaptionOrderSubmission::PRIORITY_TIME_INSENSITIVE);
        $order->setOutputFormats(array('WebVtt', 'SubRip'));
        
        $result = $order->send();

        $this->assertTrue(is_string($result));
    }
    
    public function testSendTranscriptionOrder()
    {
        $rev = $this->getClient();

        $input = $rev->uploadVideoUrl(self::MEDIA_URL);

        $order = new TranscriptionOrderSubmission($rev);

        $order->addInput($input);
        $order->includeTimestamps();
        $order->transcribeVerbatim();

        $result = $order->send();

        $this->assertTrue(is_string($result));
    }

    public function testSendTranslationOrder()
    {
        $rev = $this->getClient();

        $input = $rev->uploadDocumentUrl(self::MEDIA_URL, 400);

        $order = new TranslationOrderSubmission($rev, 'en', 'es');

        $order->addInput($input);

        $result = $order->send();

        $this->assertTrue(is_string($result));
    }
    
    public function testGetOrder()
    {
        $rev = $this->getClient();

        $input = $rev->uploadVideoUrl(self::MEDIA_URL);

        $order = new CaptionOrderSubmission($rev);

        $order->addInput($input);

        $order_number = $order->send();

        $order = $rev->getOrder($order_number);
        
        $this->assertInstanceOf('\RevAPI\Order', $order);
    }
    
    public function testGetAttachment()
    {
        $rev = $this->getClient();

        $completed_order = $this->getACompletedOrder();

        if (!$completed_order) {
            $this->markTestSkipped('Unable to find a completed order');
            return;
        }

        $attachments = $completed_order->getAttachments();

        foreach ($attachments as $attachment) {
            $via_get_attachment = $rev->getAttachment($attachment->getId());
            $this->assertEquals($attachment->getId(), $via_get_attachment->getId(), 'the two should have the same ID');
        }
    }
    
    public function testGetAttachments()
    {
        $completed_order = $this->getACompletedOrder();
        
        if (!$completed_order) {
            $this->markTestSkipped('Unable to find a completed order');
            return;
        }
        
        $attachments = $completed_order->getAttachments();

        foreach ($attachments as $attachment) {
            $this->assertNotEmpty($attachment->getName());
            
            if (!$attachment->isMedia()) {
                $docx = $attachment->getContent();
                $txt = $attachment->getContent('.txt');
                
                $this->assertTrue(is_string($docx));
                $this->assertTrue(is_string($txt));
                $this->assertNotEquals($docx, $txt);
            }
        }
    }
    
    public function testCancelOrder()
    {
        $rev = $this->getClient();

        //Create a new order to cancel
        $input = $rev->uploadVideoUrl(self::MEDIA_URL);

        $order = new CaptionOrderSubmission($rev);

        $order->addInput($input);

        $order_number = $order->send();
        
        $result = $rev->cancelOrder($order_number);
        
        //Now, cancel it (should return true)
        $this->assertEquals(true, $result);
    }

    /**
     * @throws Exception\RequestException
     */
    public function testRequestException()
    {
        $rev = $this->getClient();
        
        try {
            //Send an invalid URL
            $input = $rev->uploadVideoUrl('test');
        } catch (Exception\RequestException $e) {
            $this->assertEquals(10002, $e->getRevCode());
            $this->assertEquals('Could not retrieve media - the media URL was malformed', $e->getRevMessage());
        }
    }
    
    /**
     * Get a completed order
     * 
     * @param null $order_type
     * @return bool|mixed|Order
     */
    protected function getACompletedOrder($order_type = null)
    {
        if (!$order_type) {
            $order_type = Order::ORDER_TYPE_TRANSCRIPTION;
        }
        
        $rev = $this->getClient();
        $orders = $rev->getOrders();

        foreach ($orders as $order) {
            if ($order->isComplete() && $order->getOrderType() == $order_type) {
                return $order;
            }
        }
        
        return false;
    }
    
    protected function getClient()
    {
        return new Rev(REV_CLIENT_API_KEY, REV_USER_API_KEY, Rev::SANDBOX_HOST);
    }
    
}