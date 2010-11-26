<?php

namespace Test\SahiDriver\Accessor;

use Buzz\Browser;
use Buzz\Client\Mock;
require_once __DIR__ . '/../AbstractDriverTest.php';
require_once __DIR__ . '/../ExtendedJournal.php';
use Test\SahiDriver\AbstractDriverTest;
use Test\SahiDriver\ExtendedJournal;
require_once 'AbstractAccessorTest.php';

use Everzet\SahiDriver\Accessor;

class FormAccessorTest extends AbstractAccessorTest
{
    private $con;

    public function setUp()
    {
        $browser    = new Browser(new Mock\LIFO(), new ExtendedJournal());
        $this->con  = $this->createConnection(uniqid(), $browser, true);
    }

    public function testButton()
    {
        $accessor = new Accessor\Form\ButtonAccessor("Cancel", array(), $this->con);

        $this->assertEquals('_sahi._button("Cancel")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testButtonRelations()
    {
        $accessor1 = new Accessor\Table\TableAccessor(2, array(), $this->con);
        $accessor2 = new Accessor\Form\ButtonAccessor('Save!', array('under' => $accessor1), $this->con);

        $this->assertEquals(
            '_sahi._button("Save!", _sahi._under(_sahi._table(2)))',
            $accessor2->getAccessor()
        );
    }

    public function testOption()
    {
        $accessor = new Accessor\Form\ButtonAccessor("Cancel", array(), $this->con);

        $this->assertEquals('_sahi._button("Cancel")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testOptionRelations()
    {
        $accessor1 = new Accessor\Table\TableAccessor(2, array(), $this->con);
        $accessor2 = new Accessor\Form\ButtonAccessor('Save!', array('under' => $accessor1), $this->con);

        $this->assertEquals(
            '_sahi._button("Save!", _sahi._under(_sahi._table(2)))',
            $accessor2->getAccessor()
        );
    }

    public function testRadio()
    {
        $accessor = new Accessor\Form\RadioAccessor("id", array(), $this->con);

        $this->assertEquals('_sahi._radio("id")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertActionStep('_sahi._check(_sahi._radio("id"))', array($accessor, 'check'));
        $this->assertActionJavascript(
            '_sahi._radio("id").checked', 'true',
            array($accessor, 'isChecked')
        );
    }

    public function testRadioRelations()
    {
        $accessor1 = new Accessor\Table\TableAccessor(2, array(), $this->con);
        $accessor2 = new Accessor\Form\RadioAccessor('id', array('under' => $accessor1), $this->con);

        $this->assertEquals(
            '_sahi._radio("id", _sahi._under(_sahi._table(2)))',
            $accessor2->getAccessor()
        );
    }

    public function testCheckbox()
    {
        $accessor = new Accessor\Form\CheckboxAccessor("id", array(), $this->con);

        $this->assertEquals('_sahi._checkbox("id")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertActionStep('_sahi._check(_sahi._checkbox("id"))', array($accessor, 'check'));
        $this->assertActionStep('_sahi._uncheck(_sahi._checkbox("id"))', array($accessor, 'uncheck'));
        $this->assertActionJavascript(
            '_sahi._checkbox("id").checked', 'true',
            array($accessor, 'isChecked')
        );
    }

    public function testCheckboxRelations()
    {
        $accessor1 = new Accessor\Table\TableAccessor(2, array(), $this->con);
        $accessor2 = new Accessor\Form\CheckboxAccessor('id', array('under' => $accessor1), $this->con);

        $this->assertEquals(
            '_sahi._checkbox("id", _sahi._under(_sahi._table(2)))',
            $accessor2->getAccessor()
        );
    }

    public function testFile()
    {
        $accessor = new Accessor\Form\FileAccessor("id", array(), $this->con);

        $this->assertEquals('_sahi._file("id")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertActionStep(
            '_sahi._setFile(_sahi._file("id"), "/tmp/simple.gif")',
            array($accessor, 'setFile'),
            array('/tmp/simple.gif')
        );
    }

    public function testFileRelations()
    {
        $accessor1 = new Accessor\Table\TableAccessor(2, array(), $this->con);
        $accessor2 = new Accessor\Form\FileAccessor('id', array('under' => $accessor1), $this->con);

        $this->assertEquals(
            '_sahi._file("id", _sahi._under(_sahi._table(2)))',
            $accessor2->getAccessor()
        );
    }

    public function testHidden()
    {
        $accessor = new Accessor\Form\HiddenAccessor("Cancel", array(), $this->con);

        $this->assertEquals('_sahi._hidden("Cancel")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testHiddenRelations()
    {
        $accessor1 = new Accessor\Table\TableAccessor(2, array(), $this->con);
        $accessor2 = new Accessor\Form\HiddenAccessor('Save!', array('under' => $accessor1), $this->con);

        $this->assertEquals(
            '_sahi._hidden("Save!", _sahi._under(_sahi._table(2)))',
            $accessor2->getAccessor()
        );
    }
    
    public function testImageSubmitButtonAccessor()
    {
        $accessor = new Accessor\Form\ImageSubmitButtonAccessorAccessor("Cancel", array(), $this->con);

        $this->assertEquals('_sahi._hidden("Cancel")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testImageSubmitButtonAccessorRelations()
    {
        $accessor1 = new Accessor\Table\TableAccessor(2, array(), $this->con);
        $accessor2 = new Accessor\Form\ImageSubmitButtonAccessorAccessor('Save!', array('under' => $accessor1), $this->con);

        $this->assertEquals(
            '_sahi._hidden("Save!", _sahi._under(_sahi._table(2)))',
            $accessor2->getAccessor()
        );
    }
}
