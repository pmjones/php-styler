<?php
clone $foo;
unset($foo);
list($foo) = $bar;
empty($foo);
eval($foo);
isset($foo);
include $foo;
require 'bar';
include_once $foo;
require_once $bar;
exit();
exit(1);
die();
die(1);
$foo or throw new Exception();
static $foo, $bar;
global $foo, $bar;
echo $foo, $bar;
