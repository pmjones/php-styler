<?php
// if
if ($foo) {
    $foo = 'bar';
    // bar
}

// if else
if ($foo) {
    $foo = 'bar';
    // bar
} else {
    $foo = 'baz';
    // baz
}

// if elseif
if ($foo) {
    $foo = 'bar';
    // bar
} elseif ($bar) {
    $foo = 'baz';
    // baz
}

// if elseif else
if ($foo) {
    $foo = 'bar';
    // bar
} elseif ($bar) {
    $foo = 'baz';
    // baz
} else {
    $foo = 'dib';
    // dib
}

// if elseif elseif else
if ($foo) {
    $foo = 'bar';
    // bar
} elseif ($bar) {
    $foo = 'baz';
    // baz
} elseif ($baz) {
    $foo = 'dib';
    // dib
} else {
    $foo = 'gir';
    // gir
}

// re: issue #4, do not collapse legitimate
// `else { if ... else ... }` into `elseif { ... }`
// thereby losing the final `else`
if (true) {
    if (true) {
        // foo
    } else {
        // bar
    }
} else {
    if (true) {
        // baz
    } else {
        // dib
    }
}
