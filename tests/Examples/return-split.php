<?php
function foo()
{
    if ($bar) {
        return $this
            ->get(HiddenField::class)
            ->__invoke($name, $value, $attr, ...$__attr);
    }
}
