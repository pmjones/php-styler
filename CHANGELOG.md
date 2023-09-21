# Change Log

## NEXT

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
