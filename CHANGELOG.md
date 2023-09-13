# Change Log

## NEXT

Complete rewrite of code reassembly process using a Line object that splits into sub-Line objects, and applies splits to each Line independently.

Improved expansiveness handling.

Split rules now operate on a shared generic level rather than separate independent levels.

Styler sets default operators in constructor now; no need to call parent::setOperator() in extended Styler classes.

Something of a performance reduction (runs about 25% slower and uses about 25% more memory). When running PHP-Styler against itself:

- 0.2.0 styled 125 files in 1.234 seconds (0.0099 seconds/file, 19.31 MB peak memory usage),
- 0.3.0 this release styles 107 files in 1.308 seconds (0.0122 seconds/file, 23.61 MB peak memory usage).

## 0.2.0

Roughly 8x speed improvement from removing `php -l` linting in favor of PHP-Parser linting.

Cache is now ignored, though cache config remains.

Substantial improvements to line splitting and configurability of operators.

Still some problems with splitting when there are intervening unsplittable lines.

## 0.1.0

Initial release.
