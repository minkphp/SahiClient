<?php

namespace Test\SahiDriver;

abstract class AbstractConnectionTest extends \PHPUnit_Framework_TestCase
{
    protected function getConnectionMock()
    {
        $connection = $this->getMockBuilder('Everzet\SahiDriver\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        return $connection;
    }

    protected function assertActionCommand($cmd, array $args, array $command, array $arguments = array())
    {
        $connection = $this->getConnectionMock();
        $connection
            ->expects($this->once())
            ->method('executeCommand')
            ->with($cmd, $args);

        $command[0]->setConnection($connection);

        call_user_func_array($command, $arguments);
    }

    protected function assertActionStep($expected, array $command, array $arguments = array())
    {
        $connection = $this->getConnectionMock();
        $connection
            ->expects($this->once())
            ->method('executeStep')
            ->with($expected);

        $command[0]->setConnection($connection);

        call_user_func_array($command, $arguments);
    }

    protected function assertActionJavascript($expected, $return, array $command, array $arguments = array())
    {
        $connection = $this->getConnectionMock();
        $connection
            ->expects($this->once())
            ->method('executeJavascript')
            ->with($expected)
            ->will($this->returnValue($return));

        $command[0]->setConnection($connection);

        $this->assertEquals($return, call_user_func_array($command, $arguments));
    }
}
