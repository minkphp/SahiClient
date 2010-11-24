<?php

namespace Test\SahiDriver;

class ConnectionTest extends AbstractDriverTest
{
    /**
     * @expectedException   Everzet\SahiDriver\Exception\ConnectionException
     */
    public function testProxyNotStartedConstruct()
    {
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 404 Not Found'));
        $this->createConnection(uniqid());
    }

    /**
     * @expectedException   Everzet\SahiDriver\Exception\ConnectionException
     */
    public function testProxyNotReadyConstruct()
    {
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'false'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $this->createConnection(uniqid());
    }

    public function testExecuteCommand()
    {
        $con = $this->createConnection($sid = uniqid(), true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'all works fine'));

        $response   = $con->executeCommand('setSpeed', array('speed' => 2000, 'milli' => 'true'));
        $request    = $this->browser->getJournal()->getLastRequest();

        $this->assertEquals('all works fine', $response);
        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_setSpeed', $request->getUrl());
        $this->assertEquals('speed=2000&milli=true&sahisid='.$sid, $request->getContent());
    }

    public function testExecuteStepPass()
    {
        $con = $this->createConnection($sid = uniqid(), true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'true'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $con->executeStep('_sahi._clearLastAlert()');
        $this->assertEquals(2, count($this->browser->getJournal()));
    }

    /**
     * @expectedException   Everzet\SahiDriver\Exception\ConnectionException
     */
    public function testExecuteStepFail()
    {
        $con = $this->createConnection($sid = uniqid(), true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'error: incorrect'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $con->executeStep('_sahi._clearLastAlert()');
    }

    public function testExecuteJavascriptNotNull()
    {
        $con = $this->createConnection($sid = uniqid(), true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', '25'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'true'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $this->assertEquals(25, $con->executeJavascript('_sahi._lastConfirm()'));
        $this->assertEquals(3, count($this->browser->getJournal()));

        $request    = $this->browser->getJournal()->getLastRequest();
        $response   = $this->browser->getJournal()->getLastResponse();

        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_getVariable', $request->getUrl());
        $this->assertContains('key=___lastValue___', $request->getContent());
        $this->assertEquals('25' , $response->getContent());
    }

    public function testExecuteJavascriptNull()
    {
        $con = $this->createConnection($sid = uniqid(), true);

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
