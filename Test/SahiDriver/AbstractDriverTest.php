<?php

namespace Test\SahiDriver;

use Buzz\Browser;
use Buzz\Message;
use Buzz\Client\Mock;
use Everzet\SahiDriver\Connection;

abstract class AbstractDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create new Response.
     *
     * @param   string  $status     response status description
     * @param   string  $content    content
     *
     * @return  Response
     */
    protected function createResponse($status, $content = null)
    {
        $response = new Message\Response();
        $response->addHeader($status);

        if (null !== $content) {
            $response->setContent($content);
        }

        return $response;
    }

    /**
     * Create Sahi API Connection with custom SID.
     *
     * @param   string  $sid        sahi id
     * @param   boolean $correct    add correct responses to browser Queue for browser creation
     *
     * @return  Driver
     */
    protected function createConnection($sid, Browser $browser, $correct = false)
    {
        if ($correct) {
            $browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'true'));
            $browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));
        }

        $connection = new Connection($sid, 'localhost', 9999, $browser);

        if ($correct) {
            $browser->getJournal()->clear();
        }

        return $connection;
    }
}
