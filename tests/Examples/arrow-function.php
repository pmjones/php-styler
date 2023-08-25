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
    // this looks bad because the args get split
    // before the array gets split.
    $payload = Payload::updated(
        ['result' => $this->veryLongMethodName(
            fn () : string => $this->anotherMethodName($source, $target),
        )],
    );

    // fix by extracting the array element
    $result = $this->veryLongMethodName(
        fn () : string => $this->anotherMethodName($source, $target),
    );
    $payload = Payload::updated(['result' => $result]);
}
