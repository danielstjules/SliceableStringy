<?php

namespace SliceableStringy;

use Stringy\Stringy;

class SliceableStringy extends Stringy implements \ArrayAccess
{
    /**
     * Implements python-like string slices. Slices the string if the offset
     * contains at least a single colon. Slice notation follow the format
     * "start:stop:step". If no colon is present, returns the character at the
     * given index. Offsets may be negative to count from the last character in
     * the string. Throws an exception if the index does not exist, more than 3
     * slice args are given, or the step is 0.
     *
     * @param mixed $args The index from which to retrieve the char, or a
     *                    string with colons to return a slice
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
     * arguments for the desired slice. Start, which indicates the starting
     * index of the slice, defaults to the first character in the string if
     * step is positive, and the last character if negative. Stop, which
     * indicates the exclusive boundary of the range, defaults to the length
     * of the string if step is positive, and before the first character
     * if negative. Step allows the user to include only every nth character
     * in the result, with its sign determining the direction in which indices
     * are sampled. Throws an exception if step is equal to 0.
     *
     * @param int|null $start Optional start index of the slice
     * @param int|null $stop  Optional boundary for the slice
     * @param int|null $step  Optional rate at which to include characters
     *
     * @return SliceableStringy          A new instance containing the slice
     * @throws \InvalidArgumentException If step is equal to 0
     */
    private function getSlice($start, $stop, $step)
    {
        $length = $this->length();
        $step = (isset($step)) ? $step : 1;

        if ($step === 0) {
            throw new \InvalidArgumentException('Slice step cannot be 0');
        }

        if (isset($start)) {
            $start = $this->adjustBoundary($length, $start, $step);
        } else {
            $start = ($step > 0) ? 0 : $length - 1;
        }

        if (isset($stop)) {
            $stop = $this->adjustBoundary($length, $stop, $step);
        } else {
            $stop = ($step > 0) ? $length : -1;
        }

        // Return an empty string if the set of indices would be empty
        if (($step > 0 && $start >= $stop) || ($step < 0 && $start <= $stop)) {
            return self::create('', $this->encoding);
        }

        // Return the substring if step is 1
        if ($step === 1) {
            return $this->substr($start, $stop - $start);
        }

        // Otherwise iterate over the slice indices
        $str = '';
        foreach ($this->getIndices($start, $stop, $step) as $index) {
            $str .= (isset($this[$index])) ? $this[$index] : '';
        }

        return self::create($str, $this->encoding);
    }

    /**
     * Adjusts the start or stop boundary based on the provided length and step.
     * The logic here uses CPython's PySlice_GetIndices as a reference. See:
     * https://github.com/python-git/python/blob/master/Objects/sliceobject.c
     *
     * @param int $length   The length of the string
     * @param int $boundary Start or stop value to adjust
     * @param int $step     The step to be used with the slice
     *
     * @return int An adjusted boundary value
     */
    private function adjustBoundary($length, $boundary, $step)
    {
        if ($boundary < 0) {
            $boundary += $length;
            if ($boundary < 0) {
                $boundary = ($step < 0) ? -1 : 0;
            }
        } else if ($boundary >= $length) {
            $boundary = ($step < 0) ? $length - 1 : $length;
        }

        return $boundary;
    }

    /**
     * Returns an array of indices to be included in the slice.
     *
     * @param int $start Start index of the slice
     * @param int $stop  Boundary for the slice
     * @param int $step  Rate at which to include characters
     *
     * @return array An array of indices in the string
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
