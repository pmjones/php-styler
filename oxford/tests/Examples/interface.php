<?php
interface Foo
{
    public function foofunc() : void;

    public function foofunc2() : void;
}

interface Bar extends Baz, Dib
{
    public function zimfunc() : void;
}
