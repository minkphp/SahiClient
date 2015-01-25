<?php

namespace Test\Behat\SahiClient;

use Behat\SahiClient\Client;

class ClientTest extends AbstractConnectionTest
{
    /**
     * Client with a mocked connection.
     *
     * @var Client
     */
    private $api;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $connection;

    public function setUp()
    {
        $this->connection = $this->getConnectionMock();
        $this->api = new Client($this->connection);
    }

    /**
     * @expectedException \Behat\SahiClient\Exception\ConnectionException
     * @expectedExceptionMessage Sahi proxy seems not running
     */
    public function testProxyNotStarted()
    {
        $this->connection->expects($this->once())
            ->method('isProxyStarted')
            ->will($this->returnValue(false));

        $this->api->start();
    }

    /**
     * @expectedException \Behat\SahiClient\Exception\ConnectionException
     * @expectedExceptionMessage Can not connect to Sahi session with id
     */
    public function testStartCustomSidNotReady()
    {
        $this->connection->expects($this->once())
            ->method('isProxyStarted')
            ->will($this->returnValue(true));
        $this->connection->expects($this->once())
            ->method('isCustomSidProvided')
            ->will($this->returnValue(true));
        $this->connection->expects($this->once())
            ->method('isReady')
            ->will($this->returnValue(false));

        $this->api->start();
    }

    public function testStartCustomSidDoesNotStartConnection()
    {
        $this->connection->expects($this->once())
            ->method('isProxyStarted')
            ->will($this->returnValue(true));
        $this->connection->expects($this->once())
            ->method('isCustomSidProvided')
            ->will($this->returnValue(true));
        $this->connection->expects($this->once())
            ->method('isReady')
            ->will($this->returnValue(true));
        $this->connection->expects($this->never())
            ->method('start');

        $this->api->start();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Specify browser to run in
     */
    public function testStartWithoutBrowserName()
    {
        $this->connection->expects($this->once())
            ->method('isProxyStarted')
            ->will($this->returnValue(true));
        $this->connection->expects($this->once())
            ->method('isCustomSidProvided')
            ->will($this->returnValue(false));

        $this->api->start();
    }

    public function testStartConnection()
    {
        $this->connection->expects($this->once())
            ->method('isProxyStarted')
            ->will($this->returnValue(true));
        $this->connection->expects($this->once())
            ->method('isCustomSidProvided')
            ->will($this->returnValue(false));
        $this->connection->expects($this->once())
            ->method('start')
            ->with('firefox');
        $this->connection->expects($this->once())
            ->method('isReady')
            ->will($this->returnValue(true));

        $this->api->start('firefox');
    }

    public function testStartTwice()
    {
        $this->connection->expects($this->once())
            ->method('isProxyStarted')
            ->will($this->returnValue(true));
        $this->connection->expects($this->once())
            ->method('isCustomSidProvided')
            ->will($this->returnValue(true));
        $this->connection->expects($this->once())
            ->method('isReady')
            ->will($this->returnValue(true));

        $this->api->start();

        $this->setExpectedException('Behat\SahiClient\Exception\ConnectionException', 'Client is already started');
        $this->api->start();
    }

    /**
     * @expectedException \Behat\SahiClient\Exception\ConnectionException
     * @expectedExceptionMessage Client is not started
     */
    public function testStopNotStarted()
    {
        $this->api->stop();
    }

    public function testStopCustomSidDoesNotStopConnection()
    {
        $this->connection->expects($this->once())
            ->method('isProxyStarted')
            ->will($this->returnValue(true));
        $this->connection->expects($this->once())
            ->method('isCustomSidProvided')
            ->will($this->returnValue(true));
        $this->connection->expects($this->once())
            ->method('isReady')
            ->will($this->returnValue(true));
        $this->api->start();

        $this->connection->expects($this->never())
            ->method('stop');

        $this->api->stop();
    }

    public function testStopConnection()
    {
        $this->connection->expects($this->once())
            ->method('isProxyStarted')
            ->will($this->returnValue(true));
        $this->connection->expects($this->once())
            ->method('isCustomSidProvided')
            ->will($this->returnValue(false));
        $this->connection->expects($this->once())
            ->method('isReady')
            ->will($this->returnValue(true));
        $this->api->start('firefox');

        $this->connection->expects($this->once())
            ->method('stop');

        $this->api->stop();
    }

    public function testNavigateTo()
    {
        $this->assertActionStep(
            '_sahi._navigateTo("http:\/\/sahi.co.in")',
            array($this->api, 'navigateTo'),
            array('http://sahi.co.in')
        );

        $this->assertActionStep(
            '_sahi._navigateTo("http:\/\/sahi.co.in", true)',
            array($this->api, 'navigateTo'),
            array('http://sahi.co.in', true)
        );

        $this->assertActionStep(
            '_sahi._navigateTo("http:\/\/sahi.co.in", false)',
            array($this->api, 'navigateTo'),
            array('http://sahi.co.in', false)
        );
    }

    public function testSetSpeed()
    {
        $this->assertActionCommand(
            'setSpeed',
            array('speed' => 12),
            array($this->api, 'setSpeed'),
            array(12)
        );
    }

    /**
     * Tests, that wait method returns $conditionResult.
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

    public function testFindByText()
    {
        $accessor = $this->api->findByText('', '');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\ByTextAccessor', $accessor);
    }

    public function testFindById()
    {
        $accessor = $this->api->findById('', '');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\ByIdAccessor', $accessor);
    }

    public function testFindByXPath()
    {
        $accessor = $this->api->findByXPath('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\ByXPathAccessor', $accessor);
    }

    public function testFindDom()
    {
        $accessor = $this->api->findDom('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\DomAccessor', $accessor);
    }

    public function testFindDiv()
    {
        $accessor = $this->api->findDiv('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\DivAccessor', $accessor);
    }

    public function testFindHeader()
    {
        $accessor = $this->api->findHeader();

        $this->assertInstanceOf('Behat\SahiClient\Accessor\HeadingAccessor', $accessor);
    }

    public function testFindImage()
    {
        $accessor = $this->api->findImage('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\ImageAccessor', $accessor);
    }

    public function testFindLabel()
    {
        $accessor = $this->api->findLabel('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\LabelAccessor', $accessor);
    }

    public function testFindLink()
    {
        $accessor = $this->api->findLink('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\LinkAccessor', $accessor);
    }

    public function testFindListItem()
    {
        $accessor = $this->api->findListItem('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\ListItemAccessor', $accessor);
    }

    public function testFindSpan()
    {
        $accessor = $this->api->findSpan('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\SpanAccessor', $accessor);
    }

    public function testFindButton()
    {
        $accessor = $this->api->findButton('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\Form\ButtonAccessor', $accessor);
    }

    public function testFindCheckbox()
    {
        $accessor = $this->api->findCheckbox('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\Form\CheckboxAccessor', $accessor);
    }

    public function testFindFile()
    {
        $accessor = $this->api->findFile('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\Form\FileAccessor', $accessor);
    }

    public function testFindHidden()
    {
        $accessor = $this->api->findHidden('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\Form\HiddenAccessor', $accessor);
    }

    public function testFindImageSubmitButton()
    {
        $accessor = $this->api->findImageSubmitButton('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\Form\ImageSubmitButtonAccessor', $accessor);
    }

    public function testFindOption()
    {
        $accessor = $this->api->findOption('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\Form\OptionAccessor', $accessor);
    }

    public function testFindPassword()
    {
        $accessor = $this->api->findPassword('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\Form\PasswordAccessor', $accessor);
    }

    public function testFindRadio()
    {
        $accessor = $this->api->findRadio('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\Form\RadioAccessor', $accessor);
    }

    public function testFindReset()
    {
        $accessor = $this->api->findReset('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\Form\ResetAccessor', $accessor);
    }

    public function testFindSelect()
    {
        $accessor = $this->api->findSelect('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\Form\SelectAccessor', $accessor);
    }

    public function testFindSubmit()
    {
        $accessor = $this->api->findSubmit('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\Form\SubmitAccessor', $accessor);
    }

    public function testFindTextarea()
    {
        $accessor = $this->api->findTextarea('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\Form\TextareaAccessor', $accessor);
    }

    public function testFindTextbox()
    {
        $accessor = $this->api->findTextbox('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\Form\TextboxAccessor', $accessor);
    }

    public function testFindCell()
    {
        $accessor = $this->api->findCell('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\Table\CellAccessor', $accessor);
    }

    public function testFindRow()
    {
        $accessor = $this->api->findRow('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\Table\RowAccessor', $accessor);
    }

    public function testFindTableHeader()
    {
        $accessor = $this->api->findTableHeader('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\Table\TableHeaderAccessor', $accessor);
    }

    public function testFindTable()
    {
        $accessor = $this->api->findTable('');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\Table\TableAccessor', $accessor);
    }
}
