<?php
$foo = Relative::foo();
$bar = \Fully\Qualified::foo();
$baz = namespace\Relative::foo();

class Foo extends Relative implements RelativeInterface
{
}

class Bar extends \Fully\Qualified implements \Fully\QualifiedInterface
{
}

class Baz extends namespace\Relative implements namespace\RelativeInterface
{
}
