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

function foo()
{
    return; // end-line
}

/**
 * multi-line
 * multi-line
 * multi-line
 */
$foo = 'bar';

// set matcher callbacks
$this->htmlAttrMatcher = /** @param array<array-key, string> $matches */
function (array $matches) : string {
    return $this->htmlAttrMatcher($matches);
};
$this->jsMatcher = /** @param array<array-key, string> $matches */
function (array $matches) : string {
    return $this->jsMatcher($matches);
};
$this->cssMatcher =

/** @param array<array-key, string> $matches */
function (array $matches) : string {
    return $this->cssMatcher($matches);
};
