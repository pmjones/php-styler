<?php
function () {
    try {
        that();
    } catch (Exception $e) {
        that();
    }

    other();
};
function () {
    try {
        that();
    } finally {
        that();
    }
};
function () {
    // comment
    try {
        that();
    } catch (Exception $e) {
        that();
    } finally {
        that();
    }
};
function () {
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
};
