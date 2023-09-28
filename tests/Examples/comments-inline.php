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

    // above-line-case
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
