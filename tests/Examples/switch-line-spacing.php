<?php
function () {
    switch ($foo) {
        case 'bar':
            that();
            break;

        case 'baz':
        case 'dib':
            that();

        default:
            that();
    }

    switch ($foo) {
        case 'bar':
            that();
            break;

        case 'baz':
        case 'dib':
            that();

        // no break
        default:
            that();
    }
};
