<?php
function funcname(Foo $foo, Bar&Baz $baz, Dib|Zim $gir)
{
}

class Statics
{
    public function returnsStatic() : static
    {
    }

    public function returnsNullableStatic() : ?static
    {
    }

    public function returnsUnionStatic() : Foo|static
    {
    }
}
