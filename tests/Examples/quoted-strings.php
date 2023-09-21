<?php
// single quotes
$foo = 'Name\Space\\';
$foo = '/^foo\sbar$/';
$foo = 'this\nthat';
$foo = 'foo
    bar
    baz';
$foo = "zim\"zim\"zim";
$foo = 'zim\'zim\'zim';

// double quotes
$foo = "this\nthat";
$foo = "foo
    bar
    baz";

// double-quotes increase backslashes
$foo = "Name\\Space\\";
$foo = "/^foo\sbar\$/";
