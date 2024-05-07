<?php
function foo()
{
    if ($this) {
        that();
    }

    other();
}

function bar()
{
    // comment
    if ($this) {
        that();
    }
}

function baz()
{
    if ($this) {
        that();
    }

    if ($this) {
        that();
    }
}

function dib()
{
    if ($this) {
        that();

        if ($this) {
            that();
        }

        if ($this) {
            that();
        }
    }
}

function zim()
{
    if ($this) {
        that();

        if ($this) {
            // comment
            that();
        }

        if ($this) {
            that();
        }
    }
}

function gir()
{
    if ($this) {
        that();
    } elseif ($that) {
        other();
    } else {
        whatever();
    }
}

function irk()
{
    if ($this) {
        // comment
        that();
    } elseif ($that) {
        // comment
        other();
    } else {
        // comment
        whatever();
    }
}

if (true) {
    if (true) {
        foreach ($controllerMap as $namespace => $replacement) {
            if (
                // Allow disabling rule by setting value to false since config
                // merging have no feature to remove entries
                false == $replacement
                || ! (
                    $controller === $namespace
                    || str_starts_with($controller, $namespace . '\\')
                )
            ) {
                // whatever
            }
        }
    }
}
