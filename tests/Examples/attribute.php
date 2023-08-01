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
