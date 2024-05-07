<?php
function foo()
{
    do {
        that();
    } while (true);

    other();
}

function bar()
{
    // comment
    do {
        that();
    } while (true);
}

function baz()
{
    do {
        that();

        do {
            that();
        } while (true);

        do {
            that();
        } while (true);
    } while (true);
}

function dib()
{
    do {
        that();

        do {
            // comment
            that();
        } while (true);

        do {
            that();
        } while (true);
    } while (true);
}
