# Upgrading from PHP-Styler 0.16 to the Current Version

This guide covers what you need to change when upgrading from the 0.16.0 release
to the current version of PHP-Styler.

## Requirements

- **PHP 8.4 or later** is now required (was PHP 8.1+).
- The `nikic/php-parser` dependency has been **removed**. PHP-Styler now uses
  PHP's built-in `PhpToken` lexer directly.

Update your `composer.json`:

```
composer require --dev pmjones/php-styler 0.x@dev
```

## Configuration

The `Config` constructor no longer accepts a `Styler` instance. It now accepts
an instance of `AFormat` that controls all formatting behavior.

**Before (0.16.0):**

```php
<?php
use PhpStyler\Config;
use PhpStyler\Files;
use PhpStyler\Styler;

return new Config(
    files: new Files(__DIR__ . '/src'),
    styler: new Styler(
        lineLen: 84,
        indentLen: 4,
    ),
);
```

**After:**

```php
<?php
use PhpStyler\Config;
use PhpStyler\Files;
use PhpStyler\Format\PlainFormat;

return new Config(
    files: new Files(__DIR__ . '/src'),
    format: new PlainFormat(
        lineLen: 84,
        indentLen: 4,
    ),
);
```

The `files` parameter works the same as before. The `cache` parameter has been
removed. The new `format` parameter defaults to `PlainFormat` if omitted.

### Choosing a Format

Two built-in formats replace the old single `Styler`:

- **`PlainFormat`** — minimal formatting with no structural rules. Same-line
  braces, no import sorting, no trailing-comma normalization. The closest
  equivalent to the old default `Styler`.

- **`DeclarationFormat`** — opinionated defaults for class/interface/enum/trait
  files: next-line braces on classes and functions, same-line braces on control
  structures, import sorting, trailing-comma normalization, type ordering, and
  more.

Three **vendor formats** approximate well-known coding standards:

- `PhpStyler\Format\Vendor\Percs30Format` — PER Coding Style 3.0
- `PhpStyler\Format\Vendor\SymfonyFormat` — Symfony coding standard
- `PhpStyler\Format\Vendor\DoctrineFormat` — Doctrine coding standard


## Customization

The old customization model — extending `Styler` and overriding `s*()` methods —
is gone entirely. The new model is declarative, using three arrays on an `AFormat`
object: **Styles**, **Rules**, and **Parses**.

**Before (0.16.0):**

```php
use PhpParser\Node\Expr;
use PhpStyler\Styler;

return new Config(
    files: new Files(__DIR__ . '/src'),
    styler: new class (lineLen: 84) extends Styler {
        // override brace placement
        protected function classBrace() : void
        {
            $this->braceOnSameLine();
        }

        // override operator spacing
        protected function modOperators() : array
        {
            return [
                Expr\BooleanNot::class => ['', '!', ''],
            ];
        }

        // override trailing comma
        protected function lastSeparator() : string
        {
            return '';
        }
    },
);
```

**After:**

```php
use PhpStyler\Config;
use PhpStyler\Files;
use PhpStyler\Format\PlainFormat;
use PhpStyler\Token;

return new Config(
    files: new Files(__DIR__ . '/src'),
    format: new PlainFormat(
        lineLen: 84,
        classBracePosition: 'same_line',
        styles: [
            Token\TNot::class => ['spaceAfter' => false],
        ],
    ),
);
```

For full details on customization, see `README-CUSTOM.md`.

### Migration Cheat Sheet

| Old (0.16.0) | New |
|---|---|
| `new Styler(lineLen: 84)` | `new PlainFormat(lineLen: 84)` |
| Override `classBrace()` | `classBracePosition: 'same_line'` or `'next_line'` |
| Override `controlBrace()` | `controlBracePosition: 'same_line'` or `'next_line'` |
| Override `modOperators()` | `styles: [Token\TXxx::class => [...]]` |
| Override `lastSeparator()` | Use `NormalizeTrailingCommas` rule (or omit it) |
| Override `sReturnType()` | `returnTypeColonSpacing: true` or `false` |
| Override `functionBodyCondenseWhen()` | `functionBracePosition: 'same_line'` |
| Override individual `s*()` methods | Use `styles` array entries for the corresponding token class |
| `--debug-parser` / `--debug-printer` | Removed (no longer applicable) |


## Behavioral Changes

The new parser produces different output in several areas. After upgrading, you
should run `php-styler preview` or `php-styler diff` on representative files to
review the differences.

### Line Length

The default line length for `PlainFormat` is **88** (unchanged from 0.16.0). The
default for `DeclarationFormat` and its subclasses is **84** (5% overflow above
80, down from the old 10%).

### Control Structure Braces

Control structures (`if`, `elseif`, `else`, `for`, `foreach`, `while`, `do`)
**always** receive braces, even if the original code had none. This is a
non-configurable behavior.

### Always-Expand Behaviors

These constructs are always expanded to multiple lines (non-configurable):

- `use` import statements (one per line)
- Class constants
- Class properties
- Trait `use` declarations
- Attributes

### Always-Normalize Behaviors

These normalizations are always applied (non-configurable):

- `exit` and `die` always receive parentheses
- Non-anonymous `new` always receives parentheses (e.g., `new Foo` becomes `new Foo()`)
- Empty parentheses are removed from anonymous classes and attributes
- Leading backslashes are removed from imports
- `!=` is always used instead of `<>`
- Long type names are converted to short (`boolean` to `bool`, `integer` to `int`, etc.)
- Native function names are lowercased

### Comment Handling

The new parser preserves comments much more faithfully than the old AST-based
approach. In particular:

- End-of-line comments stay on their original lines (no longer lost or shifted).
- Comments inside argument lists, switch cases, and similar constructs are
  preserved (the old parser could lose them).
- The mangled interpolated-string problem (`"\n"` replacing actual newlines) is
  gone, since the new parser does not reconstruct strings from an AST.

### Vertical Spacing

PHP-Styler now preserves up to one blank line between statements from the
original source. Multiple consecutive blank lines are compressed to one. The old
version always compressed all vertical spacing.

### Line Splitting

The split priority order has changed:

1. Attributes
2. Arrow functions (`fn() =>`)
3. Commas (arguments, parameters, arrays, `implements`, `match` arms)
4. Loose operators (`||`, `or`, `??`, ternary `?`/`:`)
5. Tight operators (`&&`, `and`, `.`)
6. Fluent calls (`->`, `?->`, `::`)
7. `for` semicolons

### Fluent Calls

Static method chains (`Foo::bar()->baz()`) no longer split at `::` when followed
by a constant or enum case. When they do split, `::` stays with the next call:

```php
$result = DB
    ::select()
    ->where()
    ->andWhere();
```


## New Features

### `diff` Command

A new `diff` command shows a unified diff of source files vs. their styled
versions:

```
./vendor/bin/php-styler diff
```

### Rules

Structural transformations are now explicit, composable rules:

| Rule | Effect |
|---|---|
| `CollapseEmptyBody` | Collapse empty method/function bodies to `{}` on one line |
| `ConvertFromYodaConditions` | `null === $x` to `$x === null` |
| `ConvertToYodaConditions` | `$x === null` to `null === $x` |
| `MergeParenBracket` | Merge `])` onto the same line |
| `NormalizeMemberSpacing` | Normalize blank lines between class members |
| `NormalizeImports` | Remove unused and sort `use` statements |
| `NormalizeTrailingCommas` | Add/remove trailing commas based on context |
| `NormalizeTypeOrder` | Sort union/intersection types |
| `MergeParenBrace` | Merge closing paren and opening brace onto one line |
| `RemoveBom` | Remove UTF-8 byte-order mark |
| `RemoveTrailingBlankLines` | Remove trailing blank lines from blocks |

### Parse-As Substitutions

Token-class replacements applied during parsing:

| Substitution | Effect |
|---|---|
| `TArray` → `TArrayAsShort` | `array()` to `[]` |
| `TContinue` → `TContinueAsBreak` | `continue` to `break` in switches |
| `TElse` → `TElseAsElseIf` | `else if` to `elseif` |
| `THeredocStart` → `THeredocStartAsNowdoc` | Heredocs to nowdocs |
| `TList` → `TListAsArray` | `list()` to `[]` |
| `TLogicalAnd` → `TLogicalAndAsBooleanAnd` | `and` to `&&` |
| `TLogicalOr` → `TLogicalOrAsBooleanOr` | `or` to `\|\|` |
| `TPhpClosingTag` → `TPhpClosingTagRemoved` | Remove `?>` closing tags |
| `TSemicolon` → `TSemicolonSkipRepeats` | Remove repeated semicolons |
| `TStringLiteral` → `TStringLiteralAsSingleQuote` | Double-quoted to single-quoted strings |
| `TVariable` → `TVariableWithExplicitInterpolation` | Explicit `{$var}` interpolation |


## Removed Features

- The `--debug-parser` and `--debug-printer` options on the `preview` command
  are gone (there is no longer an AST or Printable layer to dump).
- The online demonstration at `php-styler.com` is no longer referenced.
- The 89 `Printable` classes, the `Visitor`, and the `Printer` have been removed.
- The `Whitespace` helper classes have been removed.
