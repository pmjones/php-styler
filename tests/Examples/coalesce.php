<?php
return $this->veryLongMethod()
    ?? $this->veryLongMethod()
    ?? $this->veryLongMethod()
    ?? $this->veryLongMethod()
    ?? $this->veryLongMethod();

// coalesce with ternary
function foo()
{
    // looks wrong
    $maxlifetime = (int) (($this->ttl instanceof \Closure
        ? ($this->ttl)()
        : $this->ttl)
        ?? \ini_get('session.gc_maxlifetime'));

    // fix by separating
    $ttl = $this->ttl instanceof \Closure ? ($this->ttl)() : $this->ttl;
    $maxlifetime = (int) $ttl ?? \ini_get('session.gc_maxlifetime');
}

class foo
{
    public function get(string $value) : ?AcceptHeaderItem
    {
        return $this->items[$value]
            ?? $this->items[explode('/', $value)[0] . '/*']
            ?? $this->items['*/*']
            ?? $this->items['*']
            ?? null;
    }
}
