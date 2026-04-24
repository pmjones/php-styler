<?php
print "hello";
print (1 + 2) * 3;
(print "hello") && false;

// method named `print` — method-call parens must be preserved
$foo->print("bar");
Foo::print("baz");

$foo = function () {
    yield from $foo;
};

@suppressError();
