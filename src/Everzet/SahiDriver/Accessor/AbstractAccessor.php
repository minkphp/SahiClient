<?php

namespace Everzet\SahiDriver\Accessor;

use Everzet\SahiDriver\Connection;

/*
 * This file is part of the SahiDriver.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Abstract Accessor.
 */
abstract class AbstractAccessor
{
    /**
     * Sahi Driver instance.
     *
     * @var     Driver
     */
    protected   $con;

    /**
     * DOM relations.
     *
     * @var     array
     */
    private     $relations = array();
    /**
     * Available element actions.
     *
     * @var     array
     */
    private     $availableActions = array(
        'click', 'rightClick', 'doubleClick', 'check', 'uncheck', 'mouseOver', 'focus', 'removeFocus', 'blur'
    );

    /**
     * Initialize Accessor.
     *
     * @param   Connection  $connection Sahi Connection
     */
    public function __construct(Connection $con)
    {
        $this->con = $con;
    }

    /**
     * Add _in DOM relation.
     *
     * @param   AbstractAccessor    $accessor   accessor for relation
     */
    public function in(AbstractAccessor $accessor)
    {
        $this->relations[] = sprintf('_sahi._in(%s)', $accessor->getAccessor());
    }

    /**
     * Add _near DOM relation.
     *
     * @param   AbstractAccessor    $accessor   accessor for relation
     */
    public function near(AbstractAccessor $accessor)
    {
        $this->relations[] = sprintf('_sahi._near(%s)', $accessor->getAccessor());
    }

    /**
     * Add _under DOM relation.
     *
     * @param   AbstractAccessor    $accessor   accessor for relation
     */
    public function under(AbstractAccessor $accessor)
    {
        $this->relations[] = sprintf('_sahi._under(%s)', $accessor->getAccessor());
    }

    /**
     * Return true if accessor has relations.
     *
     * @return  boolean
     */
    public function hasRelations()
    {
        return 0 < count($this->relations);
    }

    /**
     * Perform action on element.
     *
     * $browser->click()
     * $browser->mouseOver()
     *
     * @param   string  $action     function name
     * @param   string  $arguments  function arguments
     */
    public function __call($action, $arguments)
    {
        if (in_array($action, $this->availableActions)) {
            $this->con->executeStep(sprintf('_sahi._%s(%s)', $action, $this->getAccessor()));
        } else {
            throw new \InvalidArgumentException(
                sprintf('Unknown method called %s::%s', get_class($this), $action)
            );
        }
    }

    /**
     * Drag'n'Drop current element onto another.
     *
     * @param   AbstractAccessor    $to destination element
     */
    public function dragDrop(AbstractAccessor $to)
    {
        $this->con->executeStep(sprintf('_sahi._dragDrop(%s, %s)', $this->getAccessor(), $to->getAccessor()));
    }

    /**
     * Drag'n'Drop current element into X,Y.
     *
     * @param   integer $x  X
     * @param   integer $y  Y
     */
    public function dragDropXY($x, $y)
    {
        $this->con->executeStep(sprintf('_sahi._dragDrop(%s, %d, %d)', $this->getAccessor(), $x, $y));
    }

    /**
     * Choose option in select box.
     *
     * @param   string  $val    option value
     */
    public function choose($val)
    {
        $this->con->executeStep(sprintf('_sahi._setSelected(%s, \'%s\')', $this->getAccessor(), $val));
    }

    /**
     * Set text value.
     *
     * @param   string  $val    value
     */
    public function setValue($val)
    {
        $this->con->executeStep(sprintf('_sahi._setValue(%s, "%s")', $this->getAccessor(), $val));
    }

    /**
     * Return current text value.
     *
     * @return  string
     */
    public function getValue()
    {
        return $this->con->executeJavascript(sprintf('%s.value', $this->getAccessor()));
    }

    /**
     * Return attribute value.
     *
     * @param   string  $attr   attribute name
     *
     * @return  string
     */
    public function getAttr($attr)
    {
        return $this->con->executeJavascript(sprintf('%s.%s', $this->getAccessor(), $attr));
    }

    /**
     * Emulate setting filepath in a file input.
     *
     * @param   string  $val    file path
     */
    public function setFile($val)
    {
        $this->con->executeStep(sprintf('_sahi._setFile(%s, \'%s\')', $this->getAccessor(), $val));
    }

    /**
     * Return inner text of element.
     *
     * @return  string
     */
    public function getText()
    {
        return $this->con->executeJavascript(sprintf('_sahi._getText(%s)', $this->getAccessor()));
    }

    /**
     * Return true if checkbox/radio checked.
     *
     * @return  boolean
     */
    public function isChecked()
    {
        return "true" === $this->getAttr('checked');
    }

    /**
     * Return selected text from selectbox.
     *
     * @return  string
     */
    public function getSelectedText()
    {
        return $this->con->executeJavascript(sprintf('_sahi._getSelectedText(%s)', $this->getAccessor()));
    }

    /**
     * Return accessor string.
     *
     * @return  string
     */
    abstract public function getAccessor();

    /**
     * Return accessor string.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->getAccessor();
    }

    /**
     * Return relations Sahi arguments.
     *
     * @return  string
     */
    protected function getRelationArgumentsString()
    {
        return implode(', ', $this->relations);
    }
}
