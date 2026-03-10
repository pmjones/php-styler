<?php
function a()
{
    return;
}

function b()
{
    return 'foo';
}

function c()
{
    yield;
}

function d()
{
    yield 'foo';
}

function e()
{
    yield 'foo' => 'bar';
}

function f()
{
    goto LABEL;

    LABEL:
    $i ++;
}

function g()
{
    throw new Exception();
}
