<?php
// hanging/orphan semicolon after split return
function foo()
{
    if ($bar) {
        return $this
            ->get(HiddenField::class)
            ->__invoke($name, $value, $attr, ...$__attr);
    }
}
