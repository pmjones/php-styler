<?php
function foo($bar, string $baz, ?int $dib, T1|T2 $gir = null) : mixed
{
    $zim = 'gir';
}

function foo2()
{
}

function &bar() : string
{
}

function baz(...$gir)
{
}

function thisVeryVeryLongFunctionName(
    VeryVeryVeryLongHint $veryVeryVeryLongArg1,
    VeryVeryVeryLongHint $veryVeryVeryLongArg2,
    VeryVeryVeryLongHint $veryVeryVeryLongArg3,
    VeryVeryVeryLongHint $veryVeryVeryLongArg4,
) {
    // logic
}

function thatVeryVeryVeryLongFunctionName(
    VeryVeryVeryLongHint $veryVeryVeryLongArg1,
    VeryVeryVeryLongHint $veryVeryVeryLongArg2,
    VeryVeryVeryLongHint $veryVeryVeryLongArg3,
    VeryVeryVeryLongHint $veryVeryVeryLongArg4,
) : VeryVeryVeryLongHint
{
    // logic
}
