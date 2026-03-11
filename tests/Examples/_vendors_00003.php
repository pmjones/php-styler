<?php
for ($i = count($this->tokens) - 1; $i >= 0; $i --) {
    if (
        ! $this->tokens[$i] instanceof TSplitPoint
        && ! $this->tokens[$i] instanceof TSpace
    ) {
        return $i;
    }
}

if (true) {
    if (true) {
        if (true) {
            if (
                $firstSplit->shouldSkipFirst(count($group['positions']))
                || ($tokens[$firstPos + 1] ?? null) instanceof TMemberDoubleColon
            ) {
                array_shift($group['positions']);
            }
        }
    }
}
