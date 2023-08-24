<?php
return $this->veryLongMethod()
    ?? $this->veryLongMethod()
    ?? $this->veryLongMethod()
    ?? $this->veryLongMethod()
    ?? $this->veryLongMethod();

// coalesce line split here messes up indents.
function foo_broke()
{
    $maxlifetime = (int) (($this->ttl instanceof \Closure ? ($this->ttl)() : $this->ttl)
        ?? \ini_get('session.gc_maxlifetime')
);
}

// fix by moving a bit of code around.
function foo_fixed()
{
    $ttl = $this->ttl instanceof \Closure ? ($this->ttl)() : $this->ttl;
    $maxlifetime = (int) ($ttl ?? \ini_get('session.gc_maxlifetime'));
}
