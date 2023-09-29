<?php
echo $foo; // end-line
echo $bar; // end-line

if (1) {
    // own-line
}

/* pre-line */ echo $foo;
echo /* mid-line */ $foo;

switch ($foo) {
    // above-line-cases
    case 'foo':
        echo $foo;

    // no break
    case 'bar':
        echo $bar;
        break;
}

/**
 * multi-line
 * multi-line
 * multi-line
 */
$foo = 'bar';

// set callbacks
$foo =

/** @param array<array-key, string> $bar */
function (array $bar) : string {
    return baz($bar);
};
