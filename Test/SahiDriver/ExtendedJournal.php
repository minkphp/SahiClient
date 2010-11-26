<?php

namespace Test\SahiDriver;

use Buzz\History\Journal;

class ExtendedJournal extends Journal
{
    public function getFirst()
    {
        return $this->entries[0];
    }

    public function get($num)
    {
        return $this->entries[$num];
    }
}
