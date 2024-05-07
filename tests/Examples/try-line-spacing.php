<?php
function foo()
{
    try {
        that();
    } catch (Exception $e) {
        that();
    }

    other();
}

function bar()
{
    try {
        that();
    } finally {
        that();
    }
}

function baz()
{
    // comment
    try {
        that();
    } catch (Exception $e) {
        that();
    } finally {
        that();
    }
}

function dib()
{
    try {
        that();
    } catch (Exception $e) {
        that();
    } finally {
        that();
    }

    try {
        that();
    } catch (Exception $e) {
        that();
    } finally {
        that();
    }
}
