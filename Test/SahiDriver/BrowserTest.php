<?php

namespace Test\SahiDriver;

use Buzz;
use Buzz\Client\Mock;
use Everzet\SahiDriver\Browser;
require_once 'AbstractDriverTest.php';
require_once 'ExtendedJournal.php';

class BrowserTest extends AbstractDriverTest
{
    private $browser;
    private $api;

    public function setUp()
    {
        $this->browser  = new Buzz\Browser(new Mock\LIFO(), new ExtendedJournal());
        $this->api      = $this->createBrowser($sid = uniqid(), $this->browser);
    }

    public function testSetSpeed()
    {
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $this->api->setSpeed(222);

        $request = $this->browser->getJournal()->getLastRequest();

        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_setSpeed', $request->getUrl());
        $this->assertContains('speed=222', $request->getContent());
    }

    protected function createBrowser($sid, Buzz\Browser $browser)
    {
        return new Browser($this->createConnection($sid, $browser, true));
    }
}
