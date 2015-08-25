<?php

namespace RevAPI;

use Dshafik\GuzzleHttp\VcrHandler;

require_once __DIR__ . '/RevIntegrationTest.php';

class RevIntegrationVCRTest extends RevIntegrationTest
{
    protected function setUp()
    {
        //It is okay not to define an api key for these tests (as long as their are cassets for them)
    }

    public function testGetAttachment()
    {
        $this->markTestSkipped('VCR does not work for this test');
    }
    
    public function testGetAttachments()
    {
        $this->markTestSkipped('VCR does not work for this test');
    }

    protected function getClient()
    {
        $vcr = VcrHandler::turnOn(__DIR__ . '/fixtures/' . $this->getName() . '.json');
        $rev = new Rev(getenv('REV_CLIENT_API_KEY'), getenv('REV_USER_API_KEY'), Rev::SANDBOX_HOST, array('handler'=>$vcr));
        
        return $rev;
    }
}
