<?php

namespace Everzet\SahiDriver\Accessor;

use Everzet\SahiDriver\Exception;
use Everzet\SahiDriver\Connection;

/*
 * This file is part of the SahiDriver.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * DOM Accessor.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class DomAccessor extends AbstractAccessor
{
    /**
     * Element ID
     *
     * @var     string
     */
    protected   $id;

    /**
     * Initialize Accessor.
     *
     * @param   string      $string DOM expression
     * @param   Connection  $con    Sahi connection
     */
    public function __construct($string, Connection $con)
    {
        parent::__construct($con);

        $this->id = $string;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessor()
    {
        return sprintf('_sahi._accessor("%s")', quoted_printable_encode($this->id));
    }
}
