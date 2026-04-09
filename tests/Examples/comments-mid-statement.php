<?php
// after opening paren in method call
$result = $this->process(
    // process all
    $input,
    $output,
);

// after opening bracket in array
$arr = [
    // items list
    $first,
    $second,
];

// after comma in function args
foo(
    $a, // first arg
    $b, // second arg
    $c,
);

// after double arrow in match
$val = match ($x) {
    1
        => // case one
        "one",

    default
        => // fallback
        "other",
};

// after as/arrow in foreach
foreach (
    $items // the items
    as $key => // the key
    $value // the value
) {
    echo $value;
}

// hashed comment after opening paren
$x = foo(
    # ignore
    $bar,
);

// hashed comment after comma
bar(
    $a, # first
    $b, # second
    $c,
);

// hashed comment after arrow in match
$val = match ($x) {
    1
        => # one
        "one",

    default
        => # fallback
        "other",
};

// hashed comment in foreach
foreach (
    $items # the items
    as $key => # keyed
    $value # valued
) {
    echo $value;
}

// slashed comment in multiline expression
$x = $a
    + $b // add b
    + $c; // add c

// slashed comment in multiline boolean
$cond = $a
    && $b // check b
    || $c; // check c

// hashed in multiline expression
$x = $a
    + $b # add b
    + $c; # add c

// hashed in multiline boolean
$cond = $a
    && $b # check b
    || $c; # check c
