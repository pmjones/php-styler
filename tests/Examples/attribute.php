<?php
#[MyAttribute]
#[\MyExample\MyAttribute]
#[MyAttribute(1234)]
#[MyAttribute(value: 1234)]
#[MyAttribute(1234), MyAttribute(5678)]
#[MyAttribute(100 + 200)]
#[MyAttribute(["key" => "value"])]
#[MyAttribute(MyAttribute::VALUE)]
function foo()
{
}

function bar(
    #[MyAttribute]
    $bar,
) {
}

function baz(
    #[MyVeryVeryVeryVeryLongAttribute]
    $bar,
    #[MyVeryVeryVeryVeryLongAttribute]
    $baz,
) {
}

function dib(
    #[MyVeryVeryVeryVeryLongAttribute(
        veryLongNamedProperty1: 'foo',
        veryLongNamedProperty2: 'bar',
    )]
    $bar,
    #[MyVeryVeryVeryVeryLongAttribute]
    $baz,
) {
}
