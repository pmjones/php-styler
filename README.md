# PHP Styler

**WARNING!!!**

PHP-Styler will **completely reformat** your PHP code, discarding any previous formatting entirely.

> McCoy: What if this thing were used where [formatting] already exists?
>
> Spock: It would destroy such [formatting] in favor of its new matrix.
>
> McCoy: Its new matrix? Do you have any idea what you're saying?
>
> Spock: I was not attempting to evaulate its [aesthetic] implications.
>
> -- *Star Trek II: The Wrath of Khan* (paraphrased)

You can try an online demonstration of PHP-Styler at <http://php-styler.com/>.

* * *

## Introduction

PHP-Styler is a PHP code formatter. It parses PHP source files into tokens,
applies configurable formatting rules and styles, and reconstructs the code
with consistent horizontal spacing, vertical spacing, and automatic line
splitting.

PHP-Styler has no dependencies beyond PHP 8.4 itself (plus
[AutoShell](https://github.com/pmjones/AutoShell) for the CLI).

### Design Goals

- **Logic Preservation.** Reformatted PHP code will continue to operate as before.

- **Horizontal and Vertical Spacing.** Automatic indenting and blank-line placement.

- **Line Length Control.** Automatic splitting across multiple lines when a single line is too long.

- **Diff-Friendly.** Default output should aid noise-reduction in diffs.

- **Customizable Formats.** Change the output format using *Styles* (token-level spacing, line breaks, and casing), *Rules* (structural transformations), and *Parses* (token replacements).

- **Comment Preservation.** End-of-line comments stay on their original lines; block comments are preserved in place.

### How It Works

PHP-Styler uses a multi-stage pipeline to reformat PHP code:

1. The *Parser* tokenizes PHP source code using PHP's built-in `PhpToken`
   lexer, then maps each token to a context-specific `AToken` subclass and
   applies *Styles* (spacing, line breaks, casing) from the *Format*.

2. *Token Rules* transform the token stream — reordering modifiers, expanding
   imports, adding braces to control structures, normalizing trailing commas,
   etc.

3. The *Assembler* converts the styled token stream into a list of *Line*
   objects, tracking indentation depth.

4. The *Splitter* checks each line against the configured maximum line length,
   splitting lines that are too long by applying split points in priority
   order.

5. *Line Rules* make final adjustments (e.g., removing trailing blank lines).

6. The lines are rendered back to text with the configured end-of-line string
   and indentation.

### Styling Examples

See the [Examples](./tests/Examples) directory for a nearly-exhaustive series
of styling examples, or try the safe `preview` command on one of your own
source files.

## Usage

### Installation

Use `composer` to add PHP-Styler as a dev requirement:

```
composer require --dev pmjones/php-styler 0.x@dev
```

Copy the default `php-styler.php` config file to your package root:

```
cp ./vendor/pmjones/php-styler/resources/php-styler.php .
```

### Commands

#### preview

Safely preview how PHP-Styler will reformat a source file (does not modify
anything):

```
./vendor/bin/php-styler preview ./src/My/Source/File.php
```

#### apply

Apply formatting to all files identified in the config file, overwriting them
in place:

```
./vendor/bin/php-styler apply
```

PHP-Styler will only apply formatting to files with a modification time *later*
than the cache file. Use `--force` to format all files regardless:

```
./vendor/bin/php-styler apply --force
```

To apply styling to specific paths instead of those in the config file, pass
them as arguments:

```
./vendor/bin/php-styler apply ./src/File.php ./resources/
```

When paths are given explicitly, the cache time is not honored.

#### check

Check all configured files to see if they need formatting, without changing
anything:

```
./vendor/bin/php-styler check
```

Returns exit code `0` if all files are OK, `1` if any need styling.

#### diff

Show a unified diff of source files vs. their styled versions:

```
./vendor/bin/php-styler diff
```

All commands accept `-c` or `--config` to specify an alternative config file:

```
./vendor/bin/php-styler preview -c /path/to/other/php-styler.php ./src/File.php
```

#### Parallel Execution

The `apply`, `check`, and `diff` commands accept `-w` or `--workers` to process
files in parallel using multiple child processes:

```
./vendor/bin/php-styler apply --workers=4
./vendor/bin/php-styler check --workers=auto
```

- `--workers=1` (the default) runs sequentially, identical to previous behavior.
- `--workers=N` splits the file list into N chunks and processes them in parallel.
- `--workers=auto` detects the number of CPU cores and uses that as the worker
  count.

Parallel execution requires no additional PHP extensions — it uses `proc_open`
and works on both Linux and Windows. For small file counts (fewer than 8), the
sequential path is used regardless of the `--workers` setting.

### Configuration

The `php-styler.php` config file returns a `Config` object:

```php
<?php
use PhpStyler\Config;
use PhpStyler\Files;

return new Config(
    files: new Files(__DIR__ . '/src'),
    cache: __DIR__ . '/.php-styler.cache',
);
```

- `iterable $files` — any iterable of file paths. The `Files` class accepts
  directories and individual file paths, recursively finding `.php` files. (If
  `Files` is insufficient, try
  [Symfony Finder](https://symfony.com/doc/current/components/finder.html).)

- `?string $cache` — path to the cache file; `null` disables caching.

- `Format $format` — a `Format` instance controlling all styling behavior
  (defaults to `PlainFormat`).

Changing the config file after `apply` will invalidate the cache, causing all
files to be reformatted.

### Formats

A *Format* defines three categories of configuration:

- **Styles** — per-token spacing (`spaceBefore`, `spaceAfter`), line breaks
  (`lineBreakBefore`, `lineBreakAfter`), blank lines (`blankLineBefore`,
  `blankLineAfter`), and text casing (`case`).

- **Rules** — structural transformations applied to the token stream (e.g.,
  `NormalizeImports`, `NormalizeTrailingCommas`).

- **Parses** — token-class replacements (e.g., converting `else if` to
  `elseif`, double-quoted strings to single-quoted, `array()` to `[]`).

Plus the basic layout settings:

| Setting | Default (PlainFormat) | Description |
|---|---|---|
| `eol` | `"\n"` | End-of-line string |
| `lineLen` | 88 | Maximum line length before splitting |
| `indentLen` | 4 | Indent width in spaces |
| `indentTab` | false | Use tabs instead of spaces |

#### PlainFormat

The base format. Defines styles for all token types with sensible defaults:
same-line braces, 88-character lines, no structural rules. Use this for
minimal formatting with no code transformations.

```php
use PhpStyler\Format\PlainFormat;

return new Config(
    files: new Files(__DIR__ . '/src'),
    format: new PlainFormat(lineLen: 120),
    cache: __DIR__ . '/.php-styler.cache',
);
```

`PlainFormat` accepts these additional constructor parameters for common
adjustments:

| Parameter | Default | Values |
|---|---|---|
| `classBracePosition` | `'same_line'` | `'same_line'`, `'next_line'` |
| `functionBracePosition` | `'same_line'` | `'same_line'`, `'next_line'` |
| `controlBracePosition` | `'same_line'` | `'same_line'`, `'next_line'` |
| `keywordCase` | `'lower'` | `'lower'`, `'upper'` |
| `concatenationSpacing` | `true` | `true` (spaces around `.`), `false` (no spaces) |
| `returnTypeColonSpacing` | `true` | `true` (` : Type`), `false` (`: Type`) |
| `blankLineAfterBlock` | `false` | `true` (blank line after closing braces/semicolons), `false` |

You can also pass `styles`, `rules`, and `parseAs` arrays to override or extend
the defaults.

#### DeclarationFormat

Extends `PlainFormat` with opinionated defaults for declaration files (classes,
interfaces, enums, traits):

- Class and function braces on the next line
- Control braces on the same line
- Blank line after blocks
- Rules for import ordering, modifier ordering, type ordering, brace addition,
  trailing comma normalization, and more

#### Vendor Formats

Pre-built formats that approximate well-known coding standards:

- **`Percs30Format`** — PER Coding Style 3.0
- **`DoctrineFormat`** — Doctrine coding standard
- **`SymfonyFormat`** — Symfony coding standard

```php
use PhpStyler\Format\Vendor\Percs30Format;

return new Config(
    files: new Files(__DIR__ . '/src'),
    format: new Percs30Format(),
    cache: __DIR__ . '/.php-styler.cache',
);
```

All vendor formats accept `styles` and `rules` arrays to override their
defaults.

### Line Splitting

#### Automatic

PHP-Styler builds each statement as a single line first. If that line exceeds
the maximum length, the *Splitter* reconstructs it by applying split points in
priority order:

1. Attributes
2. Arrow functions (`fn() =>`)
3. Commas (arguments, parameters, arrays, `implements`, `match` arms)
4. Loose operators (`||`, `or`, `??`, ternary `?`/`:`)
5. Tight operators (`&&`, `and`, `.`)
6. Fluent calls (`->`, `?->`, `::`, `::$`)
7. `for` semicolons

If the first applicable rule does not make the line short enough, the next rule
is applied in addition, and so on.

Split lines receive one blank line of margin above and below.

#### Annotated

Force a statement to split expansively by adding `@php-styler-expansive`:

```php
/** @php-styler-expansive */
$foo = [
    'bar',
    'baz',
    'dib',
];
```

Recognized forms: `/** @php-styler-expansive */`, `// @php-styler-expansive`,
and multi-line docblocks.

### Avoiding Blame

After the initial reformatting, add the commit hash to a `.git-blame-ignore-revs`
file:

1. Run `php-styler apply` and commit the changes.
2. Copy the full commit hash from `git log`.
3. Create `.git-blame-ignore-revs` with that hash.
4. Configure Git: `git config blame.ignoreRevsFile .git-blame-ignore-revs`

GitHub's blame UI will also respect this file.

## Caveats

### Line Length

Even after all splitting rules are applied, a line may still be too long — for
example, if it contains a very long string literal.

### Things PHP-Styler Does Not Do

- Split or reformat comment lines
- Split quoted strings, heredocs, or nowdocs

### Horizontal Alignment

PHP-Styler will de-align horizontally-aligned code:

```php
// before
$foo = 'longish'    . $bar;
$foo = 'short'      . $bar;
$foo = 'muchlonger' . $bar;

// after
$foo = 'longish' . $bar;
$foo = 'short' . $bar;
$foo = 'muchlonger' . $bar;
```

### Vertical Spacing

PHP-Styler preserves up to one blank line between statements. Multiple
consecutive blank lines are compressed to one.

### Comparable Offerings

[PHP CS Fixer](https://cs.symfony.com/) is the category leader. It offers
extensive rule-based customization but is extremely complex internally.

Other tools include
[PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)/PHPCBF,
[ECS](https://github.com/easy-coding-standard/easy-coding-standard), and
[PHP_Beautifier](https://pear.php.net/package/PHP_Beautifier).

Formatters in other languages with similar goals:
[Black](https://black.readthedocs.io/en/stable/) (Python),
[dart_style](https://pub.dev/packages/dart_style) (Dart), and the
[Prettier PHP plugin](https://github.com/prettier/plugin-php).
