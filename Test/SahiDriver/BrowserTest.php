<?php

namespace Test\SahiDriver;

use Everzet\SahiDriver\Browser;

class BrowserTest extends AbstractDriverTest
{
    protected $api;

    public function setUp()
    {
        parent::setUp();

        $this->api = $this->createBrowser($sid = uniqid());
    }

    public function testSelectCorrectElementAccessor()
    {
        $link = $this->api->selectLink(0);
        $this->assertEquals('_sahi._link(0)', $link->getAccessor());

        $link = $this->api->selectLink('some_id');
        $this->assertEquals('_sahi._link(\'some_id\')', $link->getAccessor());

        $link = $this->api->selectImage(12);
        $this->assertEquals('_sahi._image(12)', $link->getAccessor());

        $link = $this->api->selectTextbox(13.5);
        $this->assertEquals('_sahi._textbox(13.5)', $link->getAccessor());

        $link = $this->api->selectSubmit('/regex/');
        $this->assertEquals('_sahi._submit(\'/regex/\')', $link->getAccessor());

        $link = $this->api->selectListItem('/regex/');
        $this->assertEquals('_sahi._listItem(\'/regex/\')', $link->getAccessor());
    }

    /**
     * @expectedException   Everzet\SahiDriver\Exception\AccessorException
     */
    public function testSelectIncorrectElementAccessor()
    {
        $this->api->selectIncorrect();
    }

    public function testSelectRelationalElementAccessor()
    {
        $label = $this->api->selectLabel('Agree:');
        $check = $this->api->selectCheckbox(null, array('near' => $label));

        $this->assertEquals('_sahi._checkbox(0, _sahi._near(_sahi._label(\'Agree:\')))', $check->getAccessor());

        $form  = $this->api->selectHeading3('Create new user');
        $check = $this->api->selectCheckbox(null, array('near' => $label, 'under' => $form));

        $this->assertEquals(
            '_sahi._checkbox(0, _sahi._near(_sahi._label(\'Agree:\')), _sahi._under(_sahi._heading3(\'Create new user\')))',
            $check->getAccessor()
        );
    }

    public function testSetSpeed()
    {
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $this->api->setSpeed(222);

        $request = $this->browser->getJournal()->getLastRequest();

        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_setSpeed', $request->getUrl());
        $this->assertContains('speed=222', $request->getContent());
    }

    protected function createBrowser($sid)
    {
        return new Browser($this->createConnection($sid, true));
    }
}
