# Change Log

## 0.14.0

- Can now `apply` stying to arbitrary paths by passing file and directory
  names at the command line.

## 0.13.0

- Force visibility  on class members

- Rename Property to ClassProperty

- Style ClassMethod separately from Function

## 0.12.0

- Make heredoc and nowdoc expansive in args and arrays

- Comments now track what kind of Node they are on

## 0.11.0

- Improve presentation of inline comments.

- Rename Styler::functionBodyClipWhen() to functionBodyCondenseWhen().

- Rename Styler::maybeNewline() to maybeDoubleNewline().

- In Styler, replace clip+newline idiom with forceSingleNewline() method.

## 0.10.1

Fixes a logic-breaking bug with inline docblock comments.

Previously, this source code ...

```php
// set callbacks
$foo =
    /** @param array<array-key, string> $bar */
    function (array $bar) : string {
        return baz($bar);
    };
```

... would be presented as ...

```php
// set callbacks$foo =

/** @param array<array-key, string> $bar */
function (array $bar) : string {
    return baz($bar);
};
```

... thereby breaking the code. With this fix, it is presented as ...

```php
// set callbacks
$foo =

/** @param array<array-key, string> $bar */
function (array $bar) : string {
    return baz($bar);
};
```

... which corrects the logic-breaking bug, though the presentation leaves something to be desired.

## 0.10.0

- Inline comments, including end-of-line comments, are now presented with greater fidelity to the original code.

- Other internal QA tooling changes and additions.

## 0.9.0

- Fix testing on Windows, with related Github workflow changes.

- Add `check` command, to see if any files need styling.

## 0.8.0

- Fix #4 (`else if` now presented as `elseif`)

- Rename `Styler::lastSeparatorChar()` to `lastSeparator()`

- Add methods `Styler::lastArgSeparator()`, `lastArraySeparator()`, `lastParamSeparator()`, `lastMatchSeparator()` to allow for different last-item-separators on different constructs.

- Updated docs & tests

## 0.7.0

- Add Styler::lastSeparatorChar() to specify comma (or no comma) on last item of split list.

- Fix `apply` command to honor the `--force` option again.

- Floats, integers, single-quoted strings, and non-interpolated double-quoted strings now display their original raw value, not a reconstructed value.

- A file that starts with a `return` now has the return on the same line as the opening `<?php` tag.

- Imports now get a blank line between `use` statements of different types; this means groups of `use`, `use const`, and `use function` will be visually separate from each other, though the _Styler_ will not regroup or rearrange them.

- Standalone `const` statements are now grouped together instead of getting a blank line between them; class constants still have the blank line.

- Fix name in help output.

- Handle class constants separately from non-class constants.

- Add Styler::functionBodyClipWhen().

- Change how operators get modified; was setOperators(), now modOperators().

## 0.6.0

- Modify cache sytem:

    - Use the filemetime on the cache file instead of on the config file.

    - Add Cache::$config parameter to specify cache file location.

- Consolidate open-brace and end-brace styling logic to their own methods.

- Refactor Visitor.php to put enter/leave logic in separate methods.

- Remove escaping on single-quoted strings.

- Remove escaping on non-interpolation double-quoted strings.

- Remove escaping on non-interpolation heredoc strings.

- Second and subsequent parameters with attributes are now newline-separated.

- Add basic customization document.

## 0.5.0

- Re-enable caching.

    - Config param `cache` is removed; remove it from your config file.

    - Instead, caching uses the filemtime of the config file as the cache time.

- Introduce package-specific exception class.

- Clip condition and append-string are now customizable.

## 0.4.0

- Moved spacing classes to main namespace.

- Use class names, not other strings, for nesting identifiers.

- A single array argument now nestles up with parentheses.

- Improved fluency on statics, and initial fluent property is not split.

- Consolidate types of splits.

- Clip newline above attributes.

- Mark expansives within arrays.

- Comments in arguments make arguments expansive.

- Consolidate attribute arguments into general arguments.

- Split arrow functions at `=>`.

- `new` is no longer expansive.

- Address some addcslashes() handling of newlines when escaping strings.

## 0.3.0

- Complete rewrite of code reassembly process using a Line object that splits into sub-Line objects, and applies splits to each Line independently.

- Improved expansiveness handling.

- Split rules now operate on a shared generic level rather than separate independent levels.

- Styler sets default operators in constructor now; no need to call parent::setOperator() in extended Styler classes.

- Something of a performance reduction (runs about 25% slower and uses about 25% more memory). When running PHP-Styler against itself:

    - 0.2.0 styled 125 files in 1.234 seconds (0.0099 seconds/file, 19.31 MB peak memory usage),

    - 0.3.0 this release styles 107 files in 1.308 seconds (0.0122 seconds/file, 23.61 MB peak memory usage).

## 0.2.0

- Roughly 8x speed improvement from removing `php -l` linting in favor of PHP-Parser linting.

- Cache is now ignored, though cache config remains.

- Substantial improvements to line splitting and configurability of operators.

- Still some problems with splitting when there are intervening unsplittable lines.

## 0.1.0

Initial release.
