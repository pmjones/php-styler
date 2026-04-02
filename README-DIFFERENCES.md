# Formatting Differences: 0.16.0 vs. Current

This document describes how the **formatted output** differs between PHP-Styler
0.16.0 and the current version. It focuses on what your code looks like after
formatting, not on API or configuration changes.

For migration of your configuration file, see `README-UPGRADE-FROM-0.16.md`. For
the architectural reasons behind these changes, see `README-ARCHITECTURE.md`. To
see how these differences affect your own code, run `php-styler diff` after
upgrading.

* * *

## Line Splitting

### More Split Points

Lines can now split at binary arithmetic operators (`+`, `-`, `*`, `/`, `%`) and
comparison operators (`<`, `>`, `<=`, `>=`, `==`, `!=`, `===`, `!==`, `<=>`) in
addition to the previously supported boolean, coalesce, concatenation, and
ternary operators.

**Before (0.16.0)** — long arithmetic/comparison lines could not split:

```php
$result = $this->getUriScheme() + $this->getUriHostAndPort() + $this->getUriPathAndQuery();
```

**After** — splits at operator boundaries:

```php
$result = $this->getUriScheme()
    + $this->getUriHostAndPort()
    + $this->getUriPathAndQuery();
```

### Refined Split Priorities

The old 7-level split priority system has been replaced with an 18-level system
that follows PHP operator precedence. Lower numbers split first:

| Priority | Splits at |
|---|---|
| 10 | `if`/`while`/`for`/`foreach`/`switch`/`match` parens |
| 20 | Attributes |
| 30 | `for` semicolons |
| 40 | Commas |
| 50 | `for` commas |
| 60 | `fn() =>` |
| 70 | `?`, `:`, `?:` |
| 80 | `??` |
| 90 | Array literal `[...]` |
| 100 | `match` arm `=>` |
| 110 | `\|\|` |
| 120 | `&&` |
| 130 | Comparison operators |
| 140 | `+`, `-`, `.` |
| 150 | `*`, `/`, `%` |
| 160 | `->`, `?->`, `::` |
| 170 | Args, params, expression parens |
| 180 | Array element access `$arr[...]` |

In practice, this means splits happen at more semantically appropriate points —
commas before operators, loose operators before tight operators, and so on.

### Blank Lines Around Split Groups

When a line is split into multiple lines, blank lines are automatically inserted
above and below the split group to visually separate it from surrounding code.
Blank lines are suppressed at block boundaries (after opening braces, before
closing braces), after comments and docblocks, and after attribute brackets.

### Extra Indent Inside Parentheses/Brackets

When content inside parentheses or brackets is followed by a continuation line,
the content gets one extra indent level so the closing delimiter aligns with the
continuation.

**Before (0.16.0):**

```php
$message = sprintf(
    '%s %s %s',
    $this->getMethod(),
    $this->getRequestUri(),
    $this->server->get('SERVER_PROTOCOL'),
)
    . "\r\n"
    . $this->headers
    . $cookieHeader
    . "\r\n\r\n"
    . $this->getContent();
```

**After:**

```php
$message = sprintf(
        '%s %s %s',
        $this->getMethod(),
        $this->getRequestUri(),
        $this->server->get('SERVER_PROTOCOL'),
    )
    . "\r\n"
    . $this->headers
    . $cookieHeader
    . "\r\n\r\n"
    . $this->getContent();
```

### Improved Ternary Splitting

Complex ternary expressions embedded in function arguments now split more
readably instead of producing awkward line combinations.

**Before (0.16.0):**

```php
$sourceDirs = explode('/', isset($basePath[0])
    && '/' === $basePath[0] ? substr($basePath, 1) : $basePath);
```

**After:**

```php
$sourceDirs = explode(
    '/',
    isset($basePath[0]) && '/' === $basePath[0]
        ? substr($basePath, 1)
        : $basePath,
);
```

### Coalesce Alignment

Coalesce operators inside parentheses now align at the same indent level rather
than extra-indenting.

**Before (0.16.0):**

```php
$maxlifetime = (int) (
    ($this->ttl instanceof \Closure ? ($this->ttl)() : $this->ttl)
        ?? \ini_get('session.gc_maxlifetime')
);
```

**After:**

```php
$maxlifetime = (int) (
    ($this->ttl instanceof \Closure ? ($this->ttl)() : $this->ttl)
    ?? \ini_get('session.gc_maxlifetime')
);
```

* * *

## Fluent Chains

### First Call Stays on Same Line

The first method call in a fluent chain now stays on the same line as the base
object, rather than splitting immediately. This applies to instance calls (`->`),
nullsafe calls (`?->`), and static calls (`::`).

**Before (0.16.0):**

```php
return $this
    ->get(HiddenField::class)
    ->__invoke($name, $value, $attr, ...$__attr);

$this
    ->getCollection()
    ->updateOne(
        [$this->options['id_field'] => $sessionId],
        ['$set' => $fields],
        $options,
    );
```

**After:**

```php
return $this->get(HiddenField::class)
    ->__invoke($name, $value, $attr, ...$__attr);

$this->getCollection()
    ->updateOne(
        [$this->options['id_field'] => $sessionId],
        ['$set' => $fields],
        $options,
    );
```

### Static Chains

Static method chains no longer split at `::` when followed by a constant or enum
case (e.g., `Foo::CONSTANT` or `Status::Active` remain unsplit).

* * *

## Always-Expand Constructs

### Constructs Now Always Expanded

Several constructs that could previously appear on a single line are now always
expanded to one declaration per line.

**Use imports:**

```php
// Before
use some\namespace\{ClassA, ClassB, ClassC as C};

// After
use some\namespace\ClassA;
use some\namespace\ClassB;
use some\namespace\ClassC as C;
```

**Attributes:**

```php
// Before
#[MyAttribute(1234), MyAttribute(5678)]

// After
#[MyAttribute(1234)]
#[MyAttribute(5678)]
```

**Trait use:**

```php
// Before
use SpeakWorld, SeeWorld;

// After
use SpeakWorld;

use SeeWorld;
```

**Constants:**

```php
// Before
const BAZ = 'DIB', ZIM = 'GIR';

// After
const BAZ = 'DIB';
const ZIM = 'GIR';
```

### Closure `use` No Longer Force-Expanded

When closure parameters split across lines, the `use (...)` clause is no longer
forced to expand as well. It stays compact if it fits on one line.

**Before (0.16.0):**

```php
$veryLongVariableName = function (
    $veryLongVar1,
    $veryLongVar2,
) use (
    $veryLongVar3,
    $veryLongVar4,
) {
    $i ++;
};
```

**After:**

```php
$veryLongVariableName = function (
    $veryLongVar1,
    $veryLongVar2,
) use ($veryLongVar3, $veryLongVar4) {
    $i ++;
};
```

* * *

## Spacing and Syntax Normalization

### Reference Assignment

The `&` in reference assignments is now attached to the value rather than the
operator:

```php
// Before
$a =& $b;

// After
$a = &$b;
```

### Bitwise NOT

The `~` operator no longer has a trailing space:

```php
// Before
~ $a;

// After
~$a;
```

### `list()` to Short Syntax

`list()` is converted to the short `[]` syntax (when using `DeclarationFormat`):

```php
// Before
list($foo) = $bar;

// After
[$foo] = $bar;
```

### Always-On Normalizations

These normalizations are always applied regardless of format:

- Control structures (`if`, `else`, `for`, `foreach`, `while`, `do`) always
  receive braces, even if the original code had none
- `exit` and `die` always receive parentheses
- Non-anonymous `new` always receives parentheses (e.g., `new Foo` becomes
  `new Foo()`)
- Empty parentheses are removed from anonymous classes and attributes
- Leading backslashes are removed from imports
- `!=` is always used instead of `<>`
- Long type names are converted to short (`boolean` to `bool`, `integer` to
  `int`, `double` to `float`)
- Native function names are lowercased

### Default Line Length

`DeclarationFormat` defaults to **84** characters (was 88 in 0.16.0, representing
a 5% overflow above 80 instead of the old 10%). `PlainFormat` retains the
88-character default.

* * *

## Comments, Strings, and Vertical Spacing

### Comment Preservation

The new token-based parser preserves comments much more faithfully than the old
AST-based approach:

- **End-of-line comments** stay on their original lines instead of being shifted
  to the next line or lost entirely.
- **Comments inside argument lists**, switch cases, and similar constructs are
  preserved (the old parser could lose them).
- **Switch case "no break" comments** are now indented inside the case block:

**Before (0.16.0):**

```php
switch ($foo) {
    case 'dib':
        that();

    // no break
    default:
        that();
}
```

**After:**

```php
switch ($foo) {
    case 'dib':
        that();

        // no break

    default:
        that();
}
```

### Vertical Spacing

PHP-Styler now preserves up to one blank line between statements from the
original source. Multiple consecutive blank lines are compressed to one. The old
version always compressed all vertical spacing, removing all blank lines between
statements.

### Interpolated Strings

Literal newlines in double-quoted and heredoc strings are preserved as-is. The
old AST-based parser reconstructed strings from AST nodes, converting literal
newlines to `\n` escape sequences. This no longer happens.

### Empty Closure Bodies

Empty closure bodies are no longer collapsed to `{}` on the same line by default.
This behavior is now controlled by the `CollapseEmptyBody` rule (enabled in some
vendor formats like `Percs30Format`, not in the default formats).

**Before (0.16.0):**

```php
$foo = foo(
    $value,
    function ($value) {},
);
```

**After:**

```php
$foo = foo(
    $value,
    function ($value) {
    },
);
```
