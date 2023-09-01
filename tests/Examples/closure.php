<?php
// basic
$foo = function ($bar) use ($baz) : mixed {
    $i ++;
};

// no params, no use, no typehint
$dib = function () {
    $i ++;
};

// params, use, no typehint
$veryLongVariableName = function (
    $veryLongVar1,
    $veryLongVar2,
) use (
    $veryLongVar3,
    $veryLongVar4,
) {
    $i ++;
};

// long params, no uses
$foo = function (
    $veryVeryVeryVeryLongParameter,
    $veryVeryVeryVeryLongerParameter,
    $veryVeryVeryVeryMuchLongerParameter,
) {
    // body
};

// no params, long uses
$foo = function () use (
    $veryVeryVeryVeryLongVar1,
    $veryVeryVeryVeryLongerVar2,
    $veryVeryVeryVeryMuchLongerVar3,
) {
    // body
};

// long params, long uses
$foo = function (
    $veryVeryVeryVeryLongParameter,
    $veryVeryVeryVeryLongerParameter,
    $veryVeryVeryVeryMuchLongerParameter,
) use (
    $veryVeryVeryVeryLongVar1,
    $veryVeryVeryVeryLongerVar2,
    $veryVeryVeryVeryMuchLongerVar3,
) {
    // body
};

// long params, short uses
$foo = function (
    $veryVeryVeryVeryLongParameter,
    $veryVeryVeryVeryLongerParameter,
    $veryVeryVeryVeryMuchLongerParameter,
) use (
    $var1,
) {
    // body
};

// short params, long uses
$foo = function (
    $parameter,
) use (
    $veryVeryVeryVeryLongVar1,
    $veryVeryVeryVeryLongerVar2,
    $veryVeryVeryVeryMuchLongerVar3,
) {
    // body
};
