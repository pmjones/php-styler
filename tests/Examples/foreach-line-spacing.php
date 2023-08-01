<?php
function () {
    foreach ($foo as $bar) {
        that();
    }

    other();
};
function () {
    // comment
    foreach ($foo as $bar) {
        that();
    }
};
function () {
    foreach ($foo as $bar) {
        that();

        foreach ($foo as $bar) {
            that();
        }

        foreach ($foo as $bar) {
            that();
        }
    }
};
function () {
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
};
