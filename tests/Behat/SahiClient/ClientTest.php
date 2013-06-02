<?php

namespace Test\Behat\SahiClient;

use Behat\SahiClient\Client;
require_once 'AbstractConnectionTest.php';

class ClientTest extends AbstractConnectionTest
{

    /**
     * Client with a mocked connection.
     *
     * @var Client
     */
    private $api;

    public function setUp()
    {
        $connection = $this->getConnectionMock();
        $this->api  = new Client($connection);
    }

    public function testNavigateTo()
    {
        $this->assertActionStep(
            sprintf('_sahi._navigateTo("%s")', 'http://sahi.co.in'),
            array($this->api, 'navigateTo'),
            array('http://sahi.co.in')
        );

        $this->assertActionStep(
            sprintf('_sahi._navigateTo("%s", true)', 'http://sahi.co.in'),
            array($this->api, 'navigateTo'),
            array('http://sahi.co.in', true)
        );

        $this->assertActionStep(
            sprintf('_sahi._navigateTo("%s", false)', 'http://sahi.co.in'),
            array($this->api, 'navigateTo'),
            array('http://sahi.co.in', false)
        );
    }

    public function testSetSpeed()
    {
        $this->assertActionCommand(
            'setSpeed', array('speed' => 12),
            array($this->api, 'setSpeed'),
            array(12)
        );
    }

    /**
     * Tests, that wait method returns $conditionResult.
     *
     * @return void
     * @access public
     */
    public function testWaitReturnTrue()
    {
        $connection = $this->api->getConnection();

        $connection
            ->expects($this->any())
            ->method('evaluateJavascript')
            ->with('found-element')
            ->will($this->returnValue(true));

        $this->assertTrue($this->api->wait(500, 'found-element'));
    }

    /**
     * Tests, that wait method returns $conditionResult.
     *
     * @return void
     * @access public
     */
    public function testWaitReturnFalse()
    {
        $connection = $this->api->getConnection();

        $connection
            ->expects($this->any())
            ->method('evaluateJavascript')
            ->with('not-found-element')
            ->will($this->returnValue(false));

        $this->assertFalse($this->api->wait(500, 'not-found-element'));
    }

    public function testFindByClassName()
    {
        $accessor = $this->api->findByClassName('', '');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\ByClassNameAccessor', $accessor);
    }
}
