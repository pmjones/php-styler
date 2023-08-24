<?php
// variadic param
function foo(...$bar)
{
    // code
}

// variadic function arg
$foo = foo(...$bar);

// variadic method arg
$foo = $this->foo(...$bar);

// first-class callable
$foo = foo(...);
