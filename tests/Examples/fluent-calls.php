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

    if (true) {
        if (true) {
            $foo = FooBar::fromFoo($veryVeryLongVariable, ResponseStatus::INVALID)
                ->setError(Error::ALREADY_RESPONDED);
            $bar = FooBar::fromBar($e->getResponse())
                ->setRequest($e->getRequest())
                ->setError((string) $e->getResponse()->getBody())
                ->setException($e);
            $baz = FooBar::fromBaz(
                $response,
                $overrideStatus ?? DomainStatus::UNAUTHORIZED,
            )
                ->setException($e);
            $payload = FooBar::fromResponse($e->getResponse())
                ->setRequest($e->getRequest())
                ->setError((string) $e->getResponse()->getBody())
                ->setException($e);
        }
    }
}
