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
 * Radio Element Accessor.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class RadioAccessor extends AbstractDomAccessor
{
    /**
     * Perform check on radio
     */
    public function check()
    {
        $this->con->executeStep(sprintf('_sahi._check(%s)', $this->getAccessor()));
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
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'radio';
    }
}
