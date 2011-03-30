<?php

namespace Behat\SahiClient\Accessor\Form;

use Behat\SahiClient\Exception;

/*
 * This file is part of the Behat\SahiClient.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Checkbox Element Accessor.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class CheckboxAccessor extends RadioAccessor
{
    /**
     * Perform uncheck on radio
     */
    public function uncheck()
    {
        $this->con->executeStep(sprintf('_sahi._uncheck(%s)', $this->getAccessor()));
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'checkbox';
    }
}
