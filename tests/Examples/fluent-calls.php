<?php
$result = $this
    ->veryLongPropertyName
    ->veryLongMethodName($foo, $bar)
    ->veryLongMethodName(
        $veryLongArg,
        $veryLongArg,
        $veryLongArg,
        $veryLongArg,
        $veryLongArg,
    )
    ->veryLongMethodName(new VeryLongClassName($veryLongArg, $veryLongArg))
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

if (true) {
    if (true) {
        if (true) {
            return FooBar::fromGlobals($veryVeryLongVariable, ResponseStatus::INVALID)
                ->setError(self::ERROR_ALREADY_RESPONDED);
        }
    }
}

// statics in fluent call
function static_fluency()
{
    // static method
    $result = DB::select()
        ->where()
        ->andWhere()
        ->groupBy()
        ->having()
        ->orHaving()
        ->orderBy()
        ->limit();

    // static property
    $something = ClassName::$veryLongPropertyName
        ->veryLongMethodName()
        ->veryLongMethodName();
}
