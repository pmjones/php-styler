<?php
$result = $this
    ->veryLongPropertyName
    ->veryLongMethodName(
        $foo,
        $bar,
    )
    ->veryLongMethodName(
        $veryLongArg,
        $veryLongArg,
        $veryLongArg,
        $veryLongArg,
        $veryLongArg,
    )
    ->veryLongMethodName(
        new VeryLongClassName(
            $veryLongArg,
            $veryLongArg,
        ),
    )
    ->veryLongMethodName(
        new VeryLongClassName(
            $veryLongArg,
            $veryLongArg,
            $veryLongArg,
            $veryLongArg,
            $veryLongArg,
        ),
    )
    ->veryLongPropertyName;
