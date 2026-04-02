# Change Log

## 0.18.0

- **Binary and comparison operator splitting.** Long lines now split at binary
  arithmetic operators (`+`, `-`, `*`, `/`, `%`) and comparison operators (`<`,
  `>`, `<=`, `>=`, `==`, `!=`, `===`, `!==`, `<=>`) in addition to the
  previously supported boolean, coalesce, concatenation, and ternary operators.

- **Unified split strategy system.** All split strategies — operator splits,
  paren/bracket expansion, and condition paren expansion — are now unified into
  a single priority-ordered loop using `ksort()`. The former three-phase
  approach (condition parens, operator splits, paren fallback) is replaced by
  a flat strategy map where each strategy competes by priority.

- **Refined split priorities.** The two coarse operator split priorities
  (`LOOSE_OPERATOR` and `TIGHT_OPERATOR`) have been replaced with fine-grained
  priorities that follow PHP operator precedence. Paren/bracket expansion is
  now integrated into the same priority system. Lower numbers split first:

  | Priority | Constant | Splits at |
  |---|---|---|
  | 10 | `CONDITION_PAREN` | `if`/`while`/`for`/`foreach`/`switch`/`match` parens |
  | 20 | `ATTRIBUTE` | Attributes |
  | 30 | `FOR_SEMICOLON` | `for` semicolons |
  | 40 | `COMMA` | Commas |
  | 50 | `FOR_COMMA` | `for` commas |
  | 60 | `FN_ARROW` | `fn() =>` |
  | 70 | `TERNARY` | `?`, `:`, `?:` |
  | 80 | `COALESCE` | `??` |
  | 90 | `BRACKET` | Array literal `[...]` expansion |
  | 100 | `MATCH_ARROW` | `match` arm `=>` |
  | 110 | `BOOLEAN_OR` | `\|\|` |
  | 120 | `BOOLEAN_AND` | `&&` |
  | 130 | `COMPARISON` | `<`, `>`, `<=`, `>=`, `==`, `!=`, `===`, `!==`, `<=>` |
  | 140 | `ADDITION` | `+`, `-`, `.` |
  | 150 | `MULTIPLICATION` | `*`, `/`, `%` |
  | 160 | `FLUENT` | `->`, `?->`, `::` |
  | 170 | `OTHER_PAREN` | Args, params, expression parens |
  | 180 | `ELEMENT_BRACKET` | Array element access `$arr[...]` |

- **Polymorphic expansion priorities.** Opener tokens (`TIfOpeningParen`,
  `TArrayOpeningBracket`, `TArgsOpeningParen`, etc.) now declare their own
  expansion priority via `expandPriority()`. The Splitter discovers expansions
  by calling `Line::collectExpansionPairs()` instead of filtering with
  `instanceof` checks.

- **Match `=>` splitting.** `TMatchDoubleArrow` now implements
  `TSplittableOperator`, splitting at `MATCH_ARROW` priority (100). This
  ensures match arms split at `=>` before boolean or comparison operators
  within the arm condition.

- **Blank lines around split lines.** When a line is split into multiple lines,
  blank lines are automatically inserted above and below the split group to
  visually separate it from surrounding code. Blank lines are suppressed at block
  boundaries (after opening braces, before closing braces), after comments and
  docblocks (which stay attached to the next line), after attribute brackets, and
  before opening braces in brace-position configurations. Suppression is
  controlled by `blankLineBefore`/`blankLineAfter` style values (set to `false`
  to deny), which can be overridden per-token via the `styles` constructor
  parameter.

- **Style attached to tokens.** Each `AToken` now carries its `Style` instance,
  set during parsing. The `Style` class is now `readonly`.

- **Fluent chain improvement.** The base object now stays with its first method
  call when a fluent chain is split. All fluent split types
  (`TSplitMethodCall`, `TSplitPropertyAccess`, `TSplitStaticMember`,
  `TSplitStaticMethodCall`) now consistently skip the first split position via
  `shouldSkipFirst()`. The unused `$totalPositions` parameter has been removed
  from the `shouldSkipFirst()` signature.

- **`AToken` enhancements.** New polymorphic methods on the base token class:
  - `isContent()` — returns `true` by default, `false` for `TSplit` and
    `TSpace`. Replaces ~9 scattered `instanceof` checks.
  - `expandPriority()` — returns `null` by default; opener tokens override to
    declare their expansion priority.
  - `splObjectId()` — wraps `spl_object_id($this)` for shorter call sites.

- **Splitter simplifications.** Extracted `shouldInsertBlankLine()` predicate,
  `createLine()` helper with `wasSplit` propagation, and
  `advancePastComma()` method. Combined the double-loop in `expandCommas()`
  into a single pass. Replaced string-based dispatch in opener/closer
  expansion with direct method calls.

- **Extra paren/bracket indent.** Content inside parentheses or brackets gets one
  extra indent level when followed by a continuation line, improving readability
  of nested split expressions.

- **Performance improvements.** Comma splitting and the overall `Splitter` have
  been optimized, with improvements to `Line` and `Parser` as well.

## 0.17.0

### Overview

PHP-Styler has been rewritten from the ground up; a custom token-based parser
replaces the nikic/php-parser entirely. This eliminates the only non-trivial
external dependency, fixes longstanding comment-handling bugs, introduces a
declarative customization model, and adds parallel worker capability for
increased performance. The rewrite also allows a change from the BSD-3-Clause
license to the MIT license.

### Breaking Changes

- **PHP 8.4 required.** The minimum PHP version is now 8.4 (was 8.1).

- **`nikic/php-parser` removed.** PHP-Styler now uses PHP's built-in `PhpToken`
  lexer. The only runtime dependency is `pmjones/auto-shell` for the CLI.

- **Configuration API changed.** `Config` no longer accepts a `Styler` instance.
  It accepts a `Format` instance instead:

  ```php
  // old
  new Config(styler: new Styler(lineLen: 84), files: ..., cache: ...);

  // new
  new Config(files: ..., cache: ..., format: new PlainFormat(lineLen: 84));
  ```

- **Customization model replaced.** Extending `Styler` and overriding `s*()`
  methods is gone. All customization is now declarative through `Format` objects
  using `styles`, `rules`, and `parseAs` arrays. See `UPGRADE.md` for migration
  details.

- **Default line length changed.** `DeclarationFormat` defaults to 84 characters
  (was 88). `PlainFormat` retains the 88-character default.

### New Features

#### Format System

A new `Format` interface replaces the monolithic `Styler`. Two built-in
implementations:

- **`PlainFormat`** — minimal formatting, no structural rules. Constructor
  parameters for brace placement, keyword case, concatenation spacing, return
  type colon spacing, and blank-line-after-block behavior.

- **`DeclarationFormat`** — opinionated defaults for class files: next-line
  braces, import sorting, type ordering, trailing-comma normalization, and more.

#### Vendor Formats

Pre-built formats approximating well-known coding standards:

- **`Percs30Format`** — PER Coding Style 3.0 (120-char lines, collapsed empty
  bodies, nowdoc conversion)
- **`SymfonyFormat`** — Symfony (120-char lines, yoda conditions, no
  concatenation spacing, single-quoted strings)
- **`DoctrineFormat`** — Doctrine (120-char lines, non-yoda conditions, null-last
  type ordering, blank lines before return/throw/yield)

#### Rule System

Eleven composable rules for structural transformations:

- `RemoveBom` — remove UTF-8 byte-order mark
- `NormalizeImports` — remove unused and sort `use` statements
- `OrderTypes` — sort union/intersection types (configurable priority)
- `MergeParenBracket` — merge `])` onto the same line
- `RejoinOrphans` — rejoin orphaned tokens to the previous line
- `NormalizeTrailingCommas` — add trailing commas on split lists, remove on single-line
- `RemoveTrailingBlankLines` — remove trailing blank lines from blocks
- `CollapseEmptyBody` — collapse empty class/function bodies to `{}`
- `ConvertToYodaConditions` — `$x === null` to `null === $x`
- `ConvertFromYodaConditions` — `null === $x` to `$x === null`
- `NormalizeMemberSpacing` — normalize blank lines between class members

#### Parse-As Substitutions

Token-class replacements applied during parsing — convert `array()` to `[]`,
`else if` to `elseif`, double-quoted strings to single-quoted, heredocs to
nowdocs, `and`/`or` to `&&`/`||`, remove closing PHP tags, and more.

### `diff` Command

New command to show a unified diff of source files vs. their styled versions:

```
./vendor/bin/php-styler diff
```

#### Parallel Execution

The `apply`, `check`, and `diff` commands now accept `--workers=N` (or `auto`)
to process files in parallel using multiple child processes via `proc_open`.
This requires no additional PHP extensions and works on both Linux and Windows.
With `--workers=auto` on a 12-core machine, `check` runs ~4x faster on ~1000
files.

#### Docblock Parsing

Basic docblock structure is now parsed, enabling future rules that operate on
docblock content.

### Improvements

#### Comment Preservation

The old AST-based approach could lose or misplace comments in several contexts:
inside argument lists, at the end of switch cases, on concatenation lines, and
as the sole content of blocks. The new token-based parser preserves all comments
faithfully.

#### Interpolated Strings

The old parser reconstructed double-quoted strings from AST nodes, converting
literal newlines to `\n` escape sequences. The new parser preserves the original
string content, so heredocs and interpolated strings render as written.

#### End-of-Line Comments

End-of-line comments now stay on their original lines rather than being shifted
to the next line.

#### Vertical Spacing

PHP-Styler now preserves up to one blank line between statements from the
original source. The old version always compressed all vertical spacing.

#### Line Splitting

The split priority system has been refined. Priorities are now:

1. Attributes
2. Arrow functions (`fn() =>`)
3. Commas
4. Loose operators (`||`, `or`, `??`, ternary)
5. Tight operators (`&&`, `and`, `.`)
6. Fluent calls (`->`, `?->`, `::`)
7. `for` semicolons

#### Always-On Normalizations

Several normalizations are now always applied regardless of format:

- Control structures always receive braces
- `exit`/`die` and non-anonymous `new` always receive parentheses
- Empty parentheses removed from anonymous classes and attributes
- Leading backslashes removed from imports
- `!=` always used instead of `<>`
- Long types converted to short (`boolean` → `bool`, `integer` → `int`, etc.)
- Native function names lowercased
- `use` imports, class constants, class properties, trait uses, and attributes
  always expanded to one-per-line

## 0.16.0

Previously, PHP-Styler converted `else if` to `elseif` as part of
the _Printer_ operations on parsed _Node_ objects. Now, it converts them as
part of a custom _Parser_ pre-processing step by modifying the original code
PHP tokens themselves. Doing so required a minor change to the testing of
typehints and `declare` values.

Also renamed the default config file from `resources/php-styler.dist.php` to
`resources/php-styler.php`.

## 0.15.0

- Make line control more explicit.

    - Remove Printable::$hasAttribute, $hasComment, $isFirst, hasAttribute(),
      hasComment(), and isFirst().

    - Remove Styler::$atFirstInBody, $hadComment, $hadAttribute,
      forceSingleNewline(), maybeDoubleNewline(), and rtrim().

    - The Line object now tracks if a margin above or below is advised for
      addition, and if a margin above or below is allowed.

    - The Styler methods now specify margin additions and allowances on the
      current Line object.

- Protect various Styler methods that were mistakenly public.

- Add method Styler::blankLine() to create new Line instances.

- Rename Line::newline() to Line::blankLine().

- Styler::newline() now applies one line of margin above and below auto-split
  lines, subject to margin allowances; updated tests to reflect this
  expectation.

- Fix a logic-breaking bug where final `else` gets lost after `else if`
  (refs #4).

## 0.14.0

- Can now `apply` styling to arbitrary paths by passing file and directory
  names at the command line.

## 0.13.0

- Force visibility on class members

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

