<?php
function foo()
{
    for (; ; ) {
        that();
    }

    other();
}

function bar()
{
    // comment
    for (; ; ) {
        that();
    }
}

function baz()
{
    for (; ; ) {
        that();

        for (; ; ) {
            that();
        }

        for (; ; ) {
            that();
        }
    }
}

function dib()
{
    for (; ; ) {
        that();

        for (; ; ) {
            // comment
            that();
        }

        for (; ; ) {
            that();
        }
    }
}
