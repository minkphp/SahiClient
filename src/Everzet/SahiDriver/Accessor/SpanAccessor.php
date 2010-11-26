<?php

namespace Everzet\SahiDriver\Accessor;

use Everzet\SahiDriver\Exception;

/*
 * This file is part of the SahiDriver.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Span Element Accessor.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class SpanAccessor extends AbstractDomAccessor
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'span';
    }
}
