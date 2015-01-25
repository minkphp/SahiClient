<?php

namespace Test\Behat\SahiClient\Accessor;

use Test\Behat\SahiClient\AbstractConnectionTest;
use Behat\SahiClient\Accessor;

abstract class AbstractAccessorTest extends AbstractConnectionTest
{
    protected function assertRelations(Accessor\AbstractRelationalAccessor $accessor, $selectorStart)
    {
        $con = $accessor->getConnection();

        $accessor1 = new Accessor\ByClassNameAccessor('some_class1', 'p', array(), $con);
        $accessor2 = new Accessor\ByClassNameAccessor('some_class2', 'span', array(), $con);
        $accessor3 = new Accessor\ByClassNameAccessor('some_class3', 'div', array(), $con);
        $accessor->near($accessor1)->in($accessor3)->under($accessor2);

        $this->assertEquals(
            $selectorStart .
                '_sahi._near(_sahi._byClassName("some_class1", "p")), ' .
                '_sahi._in(_sahi._byClassName("some_class3", "div")), ' .
                '_sahi._under(_sahi._byClassName("some_class2", "span")))',
            $accessor->getAccessor()
        );
    }
}
