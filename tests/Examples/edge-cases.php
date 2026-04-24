<?php
// anonymous class (TClass::$prev?->is(T_NEW))
$obj = new class {
    public function hello() : void
    {
        echo 'anon';
    }
};

// Foo::class (TClass::$prev?->is('::'))
$name = Exception::class;

// multi-attribute on class
#[A]
#[B]
class Foo
{
}

// inline attribute with multiple (TAttributeComma TInlineAttribute branch)
function multiInline(#[A] #[B] int $x) : void
{
}

// encapsed string with array access `"${arr[key]}"` or `"$arr[0]"`
$msg = "{$items[0]} and {$map[key]}";

// array() construct
$arr = [1, 2, 3];
