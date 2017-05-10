<?php

namespace SliceableStringy;

use Stringy\Stringy;

class SliceableStringy extends Stringy implements \ArrayAccess
{
    use SliceableStringyTrait;
}
