<?php
$foo = fn (array $x) => $x;
$bar = static fn () : int => $x;
set_error_handler(static fn () : bool => true);
$baz = [static fn () => 1];
$qux = (static fn () => 1)();

$veryVeryVeryVeryVeryVeryLongVariableName = array_filter(
    $cookies,
    fn (Cookie $cookie)
        => $cookie->getName() === $this->name
            && $cookie->getPath() === $this->path
            && $cookie->getDomain() === $this->domain,
);

// arrow as arg in method call in array in method call
function foo()
{
    $payload = Payload::updated([
        'result' => $this->veryLongMethodName(
            fn () : string => $this->anotherMethodName($source, $target),
        ),
    ]);
}

if (true) {
    $config = [
        Gateway::class => fn (DatabaseConnection $db) : Gateway => new Gateway($db),
    ];
}

$longArrowFunctionName = fn (LongParam $longVar1, LongParam $longVar2) : ReturnType
    => foo('bar');
