<?php

namespace Everzet\SahiDriver\Accessor;

use Everzet\SahiDriver\Connection;
use Everzet\SahiDriver\Exception;

/*
 * This file is part of the SahiDriver.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Abstract Element Accessor.
 */
class DomElement extends AbstractAccessor
{
    /**
     * Element type
     *
     * @var     string
     */
    protected   $type;
    /**
     * Element identifier
     *
     * @var     string
     */
    protected   $id;

    /**
     * List of available element types.
     *
     * @var     array
     */
    private     $availableElements = array(
        'link', 'image', 'label', 'listItem', 'div', 'span', 'spandiv', 'heading1', 'heading2', 'heading3',
        'heading4', 'heading5', 'heading6', 'title', 'cell', 'row', 'table', 'tableHeader', 'button',
        'checkbox', 'password', 'radio', 'submit', 'textbox', 'reset', 'file', 'imageSubmitButton',
        'select', 'option', 'textbox', 'hidden'
    );

    /**
     * Initialize Accessor.
     *
     * @param   Connection  $connection Sahi Connection
     */
    public function __construct($type, $id = null, array $relations = array(), Connection $con)
    {
        parent::__construct($con);

        foreach ($relations as $relation => $accessor) {
            $this->$relation($accessor);
        }

        if (!in_array($type, $this->availableElements)) {
            throw new Exception\AccessorException(sprintf('Unknown element type: %s.', $type));
        }

        $this->type = $type;
        $this->id   = $id;
    }

    /**
     * Return DOM element type.
     *
     * @return  string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return accessor string.
     *
     * @return  string
     */
    public function getAccessor()
    {
        return sprintf('_sahi._%s(%s)', $this->type, $this->getArgumentsString());
    }

    /**
     * Return comma separated Sahi DOM arguments.
     *
     * @return  string
     */
    protected function getArgumentsString()
    {
        $arguments = array($this->getIdentifierArgumentString());

        if ($this->hasRelations()) {
            $arguments[] = $this->getRelationArgumentsString();
        }

        return implode(', ', $arguments);
    }

    /**
     * Convert identificator to JavaScript id instruction.
     *
     * @param   mixed   $id element identificator
     *
     * @return  string              JavaScript id instruction
     */
    private function getIdentifierArgumentString()
    {
        if (null === $this->id) {
            return '0';
        }

        if (is_float($this->id) || is_int($this->id)) {
            return $this->id;
        }

        return "'" . $this->id . "'";
    }
}
