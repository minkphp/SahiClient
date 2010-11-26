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

class AccessorTest extends AbstractAccessorTest
{
    private $con;

    public function setUp()
    {
        $browser    = new Browser(new Mock\LIFO(), new ExtendedJournal());
        $this->con  = $this->createConnection(uniqid(), $browser, true);
    }

    public function testAccessor()
    {
        $accessor = new Accessor\Accessor('document.formName.elementName', $this->con);

        $this->assertEquals('_sahi._accessor("document.formName.elementName")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testByClassName()
    {
        $accessor = new Accessor\ByClassNameAccessor('some_class', 'div', array(), $this->con);

        $this->assertEquals('_sahi._byClassName("some_class", "div")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testByClassNameRelations()
    {
        $accessor1 = new Accessor\ByClassNameAccessor('some_class1', 'p', array(), $this->con);
        $accessor2 = new Accessor\ByClassNameAccessor('some_class2', 'span', array(), $this->con);
        $accessor3 = new Accessor\ByClassNameAccessor('some_class3', 'div', array(
            'near'  => $accessor1,
            'under' => $accessor2
        ), $this->con);

        $this->assertEquals(
            '_sahi._byClassName("some_class3", "div", ' .
                '_sahi._near(_sahi._byClassName("some_class1", "p")), ' .
                '_sahi._under(_sahi._byClassName("some_class2", "span")))',
            $accessor3->getAccessor()
        );
    }

    public function testById()
    {
        $accessor = new Accessor\ByIdAccessor('some_id', $this->con);

        $this->assertEquals('_sahi._byId("some_id")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testByText()
    {
        $accessor = new Accessor\ByTextAccessor('span text', 'span', $this->con);

        $this->assertEquals('_sahi._byText("span text", "span")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testByXPath()
    {
        $accessor = new Accessor\ByXPathAccessor('//tr[1]/td[2]', array(), $this->con);

        $this->assertEquals('_sahi._byXPath("//tr[1]/td[2]")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testByXPathRelations()
    {
        $accessor1 = new Accessor\Table\TableAccessor(2, array(), $this->con);
        $accessor2 = new Accessor\ByXPathAccessor('//tr[1]/td[2]', array('in' => $accessor1), $this->con);

        $this->assertEquals(
            '_sahi._byXPath("//tr[1]/td[2]", _sahi._in(_sahi._table(2)))',
            $accessor2->getAccessor()
        );
    }

    public function testDiv()
    {
        $accessor = new Accessor\DivAccessor(1, array(), $this->con);

        $this->assertEquals('_sahi._div(1)', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testDivRelations()
    {
        $accessor1 = new Accessor\Table\TableAccessor(2, array(), $this->con);
        $accessor2 = new Accessor\DivAccessor('id', array('under' => $accessor1), $this->con);

        $this->assertEquals(
            '_sahi._div("id", _sahi._under(_sahi._table(2)))',
            $accessor2->getAccessor()
        );
    }

    public function testHeading()
    {
        $accessor = new Accessor\HeadingAccessor(2, null, array(), $this->con);
        $this->assertEquals('_sahi._heading2(0)', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());

        $accessor = new Accessor\HeadingAccessor(null, null, array(), $this->con);
        $this->assertEquals('_sahi._heading1(0)', $accessor->getAccessor());
    }

    public function testHeadingRelations()
    {
        $accessor1 = new Accessor\DivAccessor(5, array(), $this->con);
        $accessor2 = new Accessor\HeadingAccessor(3, 'id', array('in' => $accessor1), $this->con);

        $this->assertEquals(
            '_sahi._heading3("id", _sahi._in(_sahi._div(5)))',
            $accessor2->getAccessor()
        );
    }

    public function testImage()
    {
        $accessor = new Accessor\ImageAccessor('add.gif', array(), $this->con);
        $this->assertEquals('_sahi._image("add.gif")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testImageRelations()
    {
        $accessor1 = new Accessor\DivAccessor(5, array(), $this->con);
        $accessor2 = new Accessor\ImageAccessor('avatar', array('in' => $accessor1), $this->con);

        $this->assertEquals(
            '_sahi._image("avatar", _sahi._in(_sahi._div(5)))',
            $accessor2->getAccessor()
        );
    }

    public function testLabel()
    {
        $accessor = new Accessor\LabelAccessor('Checkbox:', array(), $this->con);
        $this->assertEquals('_sahi._label("Checkbox:")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testLabelRelations()
    {
        $accessor1 = new Accessor\DivAccessor('form', array(), $this->con);
        $accessor2 = new Accessor\LabelAccessor('Fully agree:', array('in' => $accessor1), $this->con);

        $this->assertEquals(
            '_sahi._label("Fully agree:", _sahi._in(_sahi._div("form")))',
            $accessor2->getAccessor()
        );
    }

    public function testLink()
    {
        $accessor = new Accessor\LinkAccessor('visible text', array(), $this->con);
        $this->assertEquals('_sahi._link("visible text")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testLinkRelations()
    {
        $accessor1 = new Accessor\DivAccessor('form', array(), $this->con);
        $accessor2 = new Accessor\LinkAccessor('visible text', array('under' => $accessor1), $this->con);

        $this->assertEquals(
            '_sahi._link("visible text", _sahi._under(_sahi._div("form")))',
            $accessor2->getAccessor()
        );
    }

    public function testListItem()
    {
        $accessor = new Accessor\ListItemAccessor('image', array(), $this->con);
        $this->assertEquals('_sahi._listItem("image")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testListItemRelations()
    {
        $accessor1 = new Accessor\ByClassNameAccessor('news', 'ul', array(), $this->con);
        $accessor2 = new Accessor\ListItemAccessor(2, array('in' => $accessor1), $this->con);

        $this->assertEquals(
            '_sahi._listItem(2, _sahi._in(_sahi._byClassName("news", "ul")))',
            $accessor2->getAccessor()
        );
    }

    public function testSpan()
    {
        $accessor = new Accessor\SpanAccessor(1, array(), $this->con);

        $this->assertEquals('_sahi._span(1)', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testSpanRelations()
    {
        $accessor1 = new Accessor\Table\TableAccessor(2, array(), $this->con);
        $accessor2 = new Accessor\SpanAccessor('id', array('under' => $accessor1), $this->con);

        $this->assertEquals(
            '_sahi._span("id", _sahi._under(_sahi._table(2)))',
            $accessor2->getAccessor()
        );
    }
}
