<?php
$foo = "bar";
$a = "$foo";
$a = "hello $foo world";
$a = "$foo and $bar";
$a = "{$foo}";
$a = "hello {$foo} world";
$a = "$foo[0]";
$a = "$foo[key]";
$a = "{$foo['key']}";
$a = "{$foo[0]}";
$a = "$foo->bar";
$a = "{$foo->bar}";
$a = "{$foo?->bar}";
$a = "{$foo->bar->baz}";
$a = "{$foo->bar['key']}";
$a = "{$foo->method()}";
$a = "{${$name}}";
$a = "{$scheme}://{$host}{$path}";
$a = "{$a}{$b}";
$a = "%-{$max}s %s\n";

$a = <<<END
    hello $foo
    world {$bar}
    value {$obj->prop}
END;

$a = `ls -la {$dir}`;
