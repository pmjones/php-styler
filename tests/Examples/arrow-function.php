<?php
$foo = fn (array $x) => $x;
$bar = static fn () : int => $x;
$veryVeryVeryVeryVeryVeryLongVariableName = array_filter(
    $cookies,
    fn (Cookie $cookie) => $cookie->getName() === $this->name
        && $cookie->getPath() === $this->path
        && $cookie->getDomain() === $this->domain,
);
