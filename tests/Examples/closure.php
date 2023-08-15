<?php
$foo = function ($bar) use ($baz) : mixed {
    $i ++;
};
$dib = function () {
    $i ++;
};
$veryLongVariableName = function (
    $veryLongVar1,
    $veryLongVar2,
) use (
    $veryLongVar3,
    $veryLongVar4,
) {
    $i ++;
};
