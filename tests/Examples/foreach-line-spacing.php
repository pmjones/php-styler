<?php
function foo()
{
    foreach ($foo as $bar) {
        that();
    }

    other();
}

function bar()
{
    // comment
    foreach ($foo as $bar) {
        that();
    }
}

function baz()
{
    foreach ($foo as $bar) {
        that();

        foreach ($foo as $bar) {
            that();
        }

        foreach ($foo as $bar) {
            that();
        }
    }
}

function dib()
{
    foreach ($foo as $bar) {
        that();

        foreach ($foo as $bar) {
            // comment
            that();
        }

        foreach ($foo as $bar) {
            that();
        }
    }
}
