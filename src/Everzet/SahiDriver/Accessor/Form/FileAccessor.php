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
 * File Element Accessor.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class FileAccessor extends AbstractDomAccessor
{
    /**
     * Emulate setting filepath in a file input.
     *
     * @param   string  $path   file path
     */
    public function setFile($path)
    {
        $this->con->executeStep(
            sprintf('_sahi._setFile(%s, "%s")', $this->getAccessor(), quoted_printable_encode($path))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'file';
    }
}
