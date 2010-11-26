<?php

namespace Everzet\SahiDriver\Accessor\Form;

use Everzet\SahiDriver\Accessor\AbstractDomAccessor;
use Everzet\SahiDriver\Exception;

/*
 * This file is part of the SahiDriver.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Select Element Accessor.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class SelectAccessor extends AbstractDomAccessor
{
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
     * Choose option in select box.
     *
     * @param   string  $val    option value
     */
    public function choose($val, $isMultiple = null)
    {
        $arguments = array('"' . quoted_printable_encode($val) . '"');
        if (null !== $isMultiple) {
            $arguments[] = (bool) $isMultiple ? 'true' : 'false';
        }

        $this->con->executeStep(
            sprintf('_sahi._setSelected(%s, %s)', $this->getAccessor(), implode(', ', $arguments))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'select';
    }
}
