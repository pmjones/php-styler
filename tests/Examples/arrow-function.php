<?php
$foo = fn (array $x) => $x;
$bar = static fn () : int => $x;
$veryVeryVeryVeryVeryVeryLongVariableName = array_filter(
    $cookies,
    fn (Cookie $cookie) => $cookie->getName() === $this->name
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
