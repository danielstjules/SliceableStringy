<?php

namespace SliceableStringy;

use Stringy\Stringy;

class SliceableStringy extends Stringy implements \ArrayAccess
{
    /**
     * Implements python-like string slicing. Slices the string if the offset
     * contains at least a single colon. Slice notation follow the format
     * "start:stop:step". If no colon is present, returns the character at the
     * given index. Offsets may be negative to count from the last character in
     * the string. Throws an OutOfBoundsException if the index does not exist.
     *
     * @param  mixed $args The index from which to retrieve the char, or a
     *                     string with colons to return a slice
     *
     * @return SliceableStringy          The string corresponding to the index
     *                                   or slice
     * @throws \OutOfBoundsException     If a positive or negative offset does
     *                                   not exist
     * @throws \InvalidArgumentException If more than 3 slice arguments are
     *                                   given, or step is 0
     */
    public function offsetGet($args)
    {
        if (!is_string($args) || strpos($args, ':') === false) {
            return parent::offsetGet($args);
        }

        $args = explode(':', $args);

        // Too many colons, invalid slice syntax
        if (count($args) > 3) {
            throw new \InvalidArgumentException('Too many slice arguments');
        }

        // Get slice arguments
        for ($i = 0; $i < 3; $i++) {
            if (isset($args[$i]) && $args[$i] !== '') {
                $args[$i] = (int) $args[$i];
            } else {
                $args[$i] = null;
            }
        }

        return call_user_func_array([$this, 'getSlice'], $args);
    }

    /**
     * Returns a new SliceableStringy instance given start, stop and step
     * arguments for the desired slice. $start, which defaults to 0, indicates
     * the starting index of the slice. $stop, which defaults to the length of
     * the string, indicates the exclusive boundary for what to include in the
     * slice. And $step allows the user to only include every nth character.
     * All args may be positive or negative, where a negative index counts
     * back from the end of the string.
     *
     * @param  int|null $start Optional start index of the slice
     * @param  int|null $stop  Optional boundary for the slice
     * @param  int|null $step  Optional rate at which to include characters
     *
     * @return SliceableStringy A new instance containing the slice
     */
    private function getSlice($start, $stop, $step)
    {
        $length = $this->length();
        $step = (isset($step)) ? $step  : 1;

        if ($step === 0) {
            throw new \InvalidArgumentException('Slice step cannot be 0');
        } elseif ($step > 0) {
            $start = (isset($start)) ? $start : 0;
            $stop = (isset($stop)) ? $stop : $length;
        } else {
            $start = (isset($start)) ? $start : $length;
            $stop = (isset($stop)) ? $stop : 0;
        }

        if ($start < 0) $start += $length;
        if ($stop < 0) $stop += $length;

        $start = max(0, $start);
        $stop = min($stop, $length);

        // Return an empty string if the set of indexes would be empty
        if (($step > 0 && $start >= $stop) || ($step < 0 && $start <= $stop)) {
            return self::create('', $this->encoding);
        }

        // Return the substring if step is 1
        if ($step === 1) {
            return $this->substr($start, $stop - $start);
        } else if ($step < 0) {
            $stop -= 1;
        }

        // Otherwise iterate over the slice indices
        $str = '';
        foreach ($this->getIndices($start, $stop, $step) as $index) {
            $str .= $this->at($index);
        }

        return self::create($str, $this->encoding);
    }

    /**
     * Returns an array of indexes to be included in the slice.
     *
     * @param  int $start Start index of the slice
     * @param  int $stop  Boundary for the slice
     * @param  int $step  Rate at which to include characters
     *
     * @return array An array of indexes in the string
     */
    private function getIndices($start, $stop, $step)
    {
        $indices = [];

        if ($step > 0) {
            for ($i = $start; $i < $stop; $i += $step) {
                $indices[] = $i;
            }
        } else {
            for ($i = $start; $i > $stop; $i += $step) {
                $indices[] = $i;
            }
        }

        return $indices;
    }
}
