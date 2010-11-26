<?php

namespace Test\SahiDriver\Accessor;

use Buzz\Browser;
require_once __DIR__ . '/../AbstractDriverTest.php';
use Test\SahiDriver\AbstractDriverTest;
require_once __DIR__ . '/../ExtendedJournal.php';
use Test\SahiDriver\ExtendedJournal;

abstract class AbstractAccessorTest extends AbstractDriverTest
{
    protected function assertActionStep($expected, array $command, array $arguments = array())
    {
        $command[0]->getConnection()->getBrowser()->getClient()->sendToQueue(
            $this->createResponse('1.0 200 OK', 'true')
        );
        $command[0]->getConnection()->getBrowser()->getClient()->sendToQueue(
            $this->createResponse('1.0 200 OK')
        );

        $command[0]->getConnection()->getBrowser()->getJournal()->clear();
        call_user_func_array($command, $arguments);

        $this->assertEquals(2, count($command[0]->getConnection()->getBrowser()->getJournal()));

        $request    = $command[0]->getConnection()->getBrowser()->getJournal()->getFirst()->getRequest();
        $response   = $command[0]->getConnection()->getBrowser()->getJournal()->getFirst()->getResponse();
        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_setStep', $request->getUrl());
        $this->assertContains('step=' . $expected, urldecode($request->getContent()));
    }

    protected function assertActionJavascript($expected, $return, array $command, array $arguments = array())
    {
        $command[0]->getConnection()->getBrowser()->getClient()->sendToQueue(
            $this->createResponse('1.0 200 OK', $return)
        );
        $command[0]->getConnection()->getBrowser()->getClient()->sendToQueue(
            $this->createResponse('1.0 200 OK', 'true')
        );
        $command[0]->getConnection()->getBrowser()->getClient()->sendToQueue(
            $this->createResponse('1.0 200 OK')
        );

        $command[0]->getConnection()->getBrowser()->getJournal()->clear();
        $this->assertEquals($return, call_user_func_array($command, $arguments));
        $this->assertEquals(3, count($command[0]->getConnection()->getBrowser()->getJournal()));

        $request    = $command[0]->getConnection()->getBrowser()->getJournal()->getFirst()->getRequest();
        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_setStep', $request->getUrl());
        $this->assertContains('step=_sahi.setServerVarPlain(', urldecode($request->getContent()));
        $this->assertContains($expected, urldecode($request->getContent()));

        $request    = $command[0]->getConnection()->getBrowser()->getJournal()->getLastRequest();
        $response   = $command[0]->getConnection()->getBrowser()->getJournal()->getLastResponse();
        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_getVariable', $request->getUrl());
        $this->assertContains('key=___lastValue___', $request->getContent());
    }
}
