<?php

namespace Test\SahiDriver;

use Buzz\Browser;
use Buzz\Client\Mock;
require_once 'AbstractDriverTest.php';
require_once 'ExtendedJournal.php';

class ConnectionTest extends AbstractDriverTest
{
    private $browser;

    public function setUp()
    {
        $this->browser = new Browser(new Mock\LIFO(), new ExtendedJournal());
    }

    /**
     * @expectedException   Everzet\SahiDriver\Exception\ConnectionException
     */
    public function testProxyNotStartedConstruct()
    {
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 404 Not Found'));
        $this->createConnection(uniqid(), $this->browser);
    }

    /**
     * @expectedException   Everzet\SahiDriver\Exception\ConnectionException
     */
    public function testProxyNotReadyConstruct()
    {
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'false'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $this->createConnection(uniqid(), $this->browser);
    }

    public function testExecuteCommand()
    {
        $con = $this->createConnection($sid = uniqid(), $this->browser, true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'all works fine'));

        $response   = $con->executeCommand('setSpeed', array('speed' => 2000, 'milli' => 'true'));
        $request    = $this->browser->getJournal()->getLastRequest();

        $this->assertSame($this->browser, $con->getBrowser());
        $this->assertEquals('all works fine', $response);
        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_setSpeed', $request->getUrl());
        $this->assertEquals('speed=2000&milli=true&sahisid='.$sid, $request->getContent());
    }

    public function testExecuteStep()
    {
        $con = $this->createConnection($sid = uniqid(), $this->browser, true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'true'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $con->executeStep('_sahi._clearLastAlert()');

        $this->assertEquals(2, count($this->browser->getJournal()));

        $request    = $this->browser->getJournal()->getFirst()->getRequest();
        $response   = $this->browser->getJournal()->getFirst()->getResponse();
        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_setStep', $request->getUrl());
        $this->assertContains('step=' . urlencode('_sahi._clearLastAlert()'), $request->getContent());
    }

    /**
     * @expectedException   Everzet\SahiDriver\Exception\ConnectionException
     */
    public function testExecuteStepFail()
    {
        $con = $this->createConnection($sid = uniqid(), $this->browser, true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'error: incorrect'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $con->executeStep('_sahi._clearLastAlert()');
    }

    public function testExecuteJavascript()
    {
        $con = $this->createConnection($sid = uniqid(), $this->browser, true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', '25'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'true'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $this->assertEquals(25, $con->executeJavascript('_sahi._lastConfirm()'));
        $this->assertEquals(3, count($this->browser->getJournal()));

        $request    = $this->browser->getJournal()->getFirst()->getRequest();
        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_setStep', $request->getUrl());
        $this->assertContains('step=' . urlencode('_sahi.setServerVarPlain('), $request->getContent());
        $this->assertContains(urlencode('_sahi._lastConfirm()'), $request->getContent());

        $request    = $this->browser->getJournal()->getLastRequest();
        $response   = $this->browser->getJournal()->getLastResponse();
        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_getVariable', $request->getUrl());
        $this->assertContains('key=___lastValue___', $request->getContent());
        $this->assertEquals('25' , $response->getContent());
    }

    public function testLongExecuteJavascript()
    {
        $con = $this->createConnection($sid = uniqid(), $this->browser, true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', '22'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'true'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'false'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'false'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $this->assertEquals(22, $con->executeJavascript('_sahi._lastConfirm()'));
        $this->assertEquals(5, count($this->browser->getJournal()));

        $request    = $this->browser->getJournal()->getFirst()->getRequest();
        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_setStep', $request->getUrl());
        $this->assertContains('step=' . urlencode('_sahi.setServerVarPlain('), $request->getContent());
        $this->assertContains(urlencode('_sahi._lastConfirm()'), $request->getContent());

        $request    = $this->browser->getJournal()->getLastRequest();
        $response   = $this->browser->getJournal()->getLastResponse();
        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_getVariable', $request->getUrl());
        $this->assertContains('key=___lastValue___', $request->getContent());
        $this->assertEquals('22' , $response->getContent());
    }

    /**
     * @expectedException   Everzet\SahiDriver\Exception\ConnectionException
     */
    public function tesExecuteJavascriptError()
    {
        $con = $this->createConnection($sid = uniqid(), $this->browser, true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'error: incorrect'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $con->executeJavascript('_sahi._lastConfirm()');
    }

    public function testExecuteJavascriptNull()
    {
        $con = $this->createConnection($sid = uniqid(), $this->browser, true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'null'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'true'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $this->assertNull($con->executeJavascript('_sahi._lastConfirm()'));
        $this->assertEquals(3, count($this->browser->getJournal()));

        $request    = $this->browser->getJournal()->getLastRequest();
        $response   = $this->browser->getJournal()->getLastResponse();

        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_getVariable', $request->getUrl());
        $this->assertContains('key=___lastValue___', $request->getContent());
        $this->assertEquals('null', $response->getContent());
    }
}
