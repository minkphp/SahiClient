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

class SharedActionsTest extends AbstractAccessorTest
{
    public function getAccessors()
    {
        $browser    = new Browser(new Mock\LIFO(), new ExtendedJournal());
        $connection = $this->createConnection(uniqid(), $browser, true);

        return array(
            array(
                $first = new Accessor\Accessor('document.formName.elementName', $connection),
                '_sahi._accessor("document.formName.elementName")'
            ),
            array(
                new Accessor\LabelAccessor('Check:', array('near' => $first), $connection),
                '_sahi._label("Check:", _sahi._near(_sahi._accessor("document.formName.elementName")))'
            )
        );
    }

    /**
     * @dataProvider    getAccessors
     */
    public function testClickActions(Accessor\AbstractAccessor $accessor, $selector)
    {
        $this->assertActionStep('_sahi._click(' . $selector . ')', array($accessor, 'click'));
        $this->assertActionStep('_sahi._rightClick(' . $selector . ')', array($accessor, 'rightClick'));
        $this->assertActionStep('_sahi._doubleClick(' . $selector . ')', array($accessor, 'doubleClick'));
    }

    /**
     * @dataProvider    getAccessors
     */
    public function testMouseAndFocusActions(Accessor\AbstractAccessor $accessor, $selector)
    {
        $this->assertActionStep('_sahi._mouseOver(' . $selector . ')', array($accessor, 'mouseOver'));
        $this->assertActionStep('_sahi._focus(' . $selector . ')', array($accessor, 'focus'));
        $this->assertActionStep('_sahi._removeFocus(' . $selector . ')', array($accessor, 'removeFocus'));
        $this->assertActionStep('_sahi._blur(' . $selector . ')', array($accessor, 'blur'));
    }

    /**
     * @dataProvider    getAccessors
     */
    public function testDragDropActions(Accessor\AbstractAccessor $accessor, $selector)
    {
        $aim = new Accessor\Accessor('document.formName', $accessor->getConnection());

        $this->assertActionStep(
            '_sahi._dragDrop(' . $selector . ', _sahi._accessor("document.formName"))',
            array($accessor, 'dragDrop'),
            array($aim)
        );

        $this->assertActionStep(
            '_sahi._dragDropXY(' . $selector . ', 10, 15)',
            array($accessor, 'dragDropXY'),
            array(10, 15)
        );
        $this->assertActionStep(
            '_sahi._dragDropXY(' . $selector . ', 10, 15, true)',
            array($accessor, 'dragDropXY'),
            array(10, 15, true)
        );
        $this->assertActionStep(
            '_sahi._dragDropXY(' . $selector . ', 10, 15, false)',
            array($accessor, 'dragDropXY'),
            array(10, 15, false)
        );
    }

    /**
     * @dataProvider    getAccessors
     */
    public function testKeyActions(Accessor\AbstractAccessor $accessor, $selector)
    {
        $this->assertActionStep(
            '_sahi._keyPress(' . $selector . ', "b")',
            array($accessor, 'keyPress'),
            array('b')
        );
        $this->assertActionStep(
            '_sahi._keyPress(' . $selector . ', 98)',
            array($accessor, 'keyPress'),
            array(98)
        );
        $this->assertActionStep(
            '_sahi._keyPress(' . $selector . ', [13,13])',
            array($accessor, 'keyPress'),
            array(array(13, 13))
        );

        $this->assertActionStep(
            '_sahi._keyDown(' . $selector . ', "b")',
            array($accessor, 'keyDown'),
            array('b')
        );
        $this->assertActionStep(
            '_sahi._keyDown(' . $selector . ', 98)',
            array($accessor, 'keyDown'),
            array(98)
        );
        $this->assertActionStep(
            '_sahi._keyDown(' . $selector . ', [13,13])',
            array($accessor, 'keyDown'),
            array(array(13, 13))
        );

        $this->assertActionStep(
            '_sahi._keyUp(' . $selector . ', "b")',
            array($accessor, 'keyUp'),
            array('b')
        );
        $this->assertActionStep(
            '_sahi._keyUp(' . $selector . ', 98)',
            array($accessor, 'keyUp'),
            array(98)
        );
        $this->assertActionStep(
            '_sahi._keyUp(' . $selector . ', [13,13])',
            array($accessor, 'keyUp'),
            array(array(13, 13))
        );
    }

    /**
     * @dataProvider    getAccessors
     */
    public function testValueActions(Accessor\AbstractAccessor $accessor, $selector)
    {
        $this->assertActionStep(
            '_sahi._setValue(' . $selector . ', "Some text \"ecraned\"")',
            array($accessor, 'setValue'),
            array('Some text \"ecraned\"')
        );

        $this->assertActionJavascript(
            $selector . '.value', '23',
            array($accessor, 'getValue')
        );
    }

    /**
     * @dataProvider    getAccessors
     */
    public function testGetAttr(Accessor\AbstractAccessor $accessor, $selector)
    {
        $this->assertActionJavascript(
            $selector . '.checked', 'true',
            array($accessor, 'getAttr'),
            array('checked')
        );
    }

    /**
     * @dataProvider    getAccessors
     */
    public function testGetText(Accessor\AbstractAccessor $accessor, $selector)
    {
        $this->assertActionJavascript(
            '_sahi._getText(' . $selector . ')', 'Some text',
            array($accessor, 'getText')
        );
    }

    /**
     * @dataProvider    getAccessors
     */
    public function testHighlight(Accessor\AbstractAccessor $accessor, $selector)
    {
        $this->assertActionStep('_sahi._highlight(' . $selector . ')', array($accessor, 'highlight'));
    }
}
