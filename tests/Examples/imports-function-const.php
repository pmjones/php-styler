<?php
use Foo\SomeClass;

use const PHP_EOL;
use const Foo\BAR_CONST;
use const Foo\LONG_CONST_NAME as SHORT;

use function strlen;
use function Foo\bar;
use function Foo\longFunctionName as shortFn;

bar();
echo BAR_CONST;
echo strlen("hello");
echo PHP_EOL;
shortFn();
echo SHORT;
new SomeClass();
