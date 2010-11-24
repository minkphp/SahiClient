<?php

namespace Test\SahiDriver;

use Everzet\SahiDriver\Connection;
use Everzet\SahiDriver\Browser;

class ProxyRunedTest extends \PHPUnit_Framework_TestCase
{
    protected $browser;

    public function setUp()
    {
        $con = new Connection('testSahiDriverPHP');

        $this->browser = new Browser($con);
    }

    public function testGoogle()
    {
        $this->browser->navigateTo('http://www.google.com');

        $text = $this->browser->selectTextbox('q');
        $text->setValue('Behat BDD PHP');

        $this->browser->selectSubmit('Google Search')->click();
        $this->browser->selectLink('Behat - BDD in PHP')->click();

        $this->assertEquals('Install', $this->browser->selectHeading2(2)->getText());
    }
}
