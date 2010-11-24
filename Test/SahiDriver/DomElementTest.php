<?php

namespace Test\SahiDriver;

use Everzet\SahiDriver\Accessor\DomElement;

class DomElementTest extends AbstractDriverTest
{
    protected $api;

    public function setUp()
    {
        parent::setUp();

        $this->con = $this->createConnection(uniqid(), true);
    }

    public function testExistingTypeCreation()
    {
        $link = new DomElement('submit', null, array(), $this->con);

        $this->assertEquals('submit', $link->getType());
        $this->assertEquals('_sahi._submit(0)', $link->getAccessor());

        $link = new DomElement('hidden', 2, array(), $this->con);

        $this->assertEquals('hidden', $link->getType());
        $this->assertEquals('_sahi._hidden(2)', $link->getAccessor());

        $link = new DomElement('textarea', 'body', array(), $this->con);

        $this->assertEquals('textarea', $link->getType());
        $this->assertEquals('_sahi._textarea(\'body\')', $link->getAccessor());
    }

    /**
     * @expectedException   Everzet\SahiDriver\Exception\AccessorException
     */
    public function testUnknownTypeCreation()
    {
        $link = new DomElement('unknown', null, array(), $this->con);
    }

    public function testDifferentIdsTypeCreation()
    {
        $link = new DomElement('heading5', '/some.*Ex/', array(), $this->con);

        $this->assertEquals('heading5', $link->getType());
        $this->assertEquals('_sahi._heading5(\'/some.*Ex/\')', $link->getAccessor());

        $link = new DomElement('image', 'photo', array(), $this->con);

        $this->assertEquals('image', $link->getType());
        $this->assertEquals('_sahi._image(\'photo\')', $link->getAccessor());

        $link = new DomElement('option', 5, array(), $this->con);

        $this->assertEquals('option', $link->getType());
        $this->assertEquals('_sahi._option(5)', $link->getAccessor());
    }

    public function testRelationalTypeCreation()
    {
        $formHead = new DomElement('heading3', 3, array(), $this->con);
        $checkbox = new DomElement('checkbox', null, array('under' => $formHead), $this->con);

        $this->assertEquals('_sahi._checkbox(0, _sahi._under(_sahi._heading3(3)))', $checkbox->getAccessor());

        $label    = new DomElement('label', 'Agree:', array(), $this->con);
        $checkbox = new DomElement('checkbox', null, array(
            'under' => $formHead,
            'near'  => $label
        ), $this->con);

        $this->assertEquals('_sahi._checkbox(0, _sahi._under(_sahi._heading3(3)), _sahi._near(_sahi._label(\'Agree:\')))',
            $checkbox->getAccessor()
        );
    }

    public function testClick()
    {
        $link = new DomElement('link', '/some.*Ex/', array(), $this->con);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'true'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $link->click();
    }
}
