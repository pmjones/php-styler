# Customizing PHP-Styler

## Overview

1. [Custom _Styler_ class](#custom-styler-class)

2. [Method Overrides](#method-overrides)

3. [Operator Spacing](#operator-spacing)

4. [Brace Placement](#brace-placement)

5. [Trailing Comma](#trailing-comma)

6. [Function Signatures](#function-signatures)

7. [Finished Output](#finished-output)


## Custom Styler Class

The easiest way to start is with an empty anonymous extension of _Styler_ in your `php-styler.php` config file; remember to include various _PhpParser_ amd _PhpStyler_ imports.

```php
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpStyler\Config;
use PhpStyler\Files;
use PhpStyler\Printable as P;
use PhpStyler\Printable\Printable;
use PhpStyler\Styler;

$styler = new class (lineLen: 88) extends Styler {
};

return new Config(
    files: new Files(__DIR__ . '/src'),
    styler: $styler
);
```

> You might also create an entirely separate class, then load and instantiate it as the `$styler`.

Then invoke the `php-styler apply` command to make sure it works as the standard _Styler_.

## Method Overrides

In general, override the `Styler::s*()` method for styling the relevant _Printable_. See the _Styler_ itself to get an idea of the very large number of methods available for override. There is really quite a lot here; you will be well-served by experimenting with trial-and-error when attempting customizations.

## Operator Spacing

The `$this->operators` property describes the spacing around operation strings. The `$operators` property is used by the `Styler::sInfix*()`, `Styler::sPrefix*()`, and `Styler::sPostfix*()` method families.

Each `$operators` key is the class name of the operation, and each value is a three-element array consisting of the space before the operator, the operator itself, and the space after the operator.

You can set the spacing around operators by overriding the `Styler::setOperators()` method and modifying `$this->operators`. (Cf. `Styler::__construct()` for all operator strings.) For example, to make sure there is no space around `!`:

```php
    protected function setOperators() : void
    {
        $this->operators[Expr\BooleanNot::class] = ['', '!', ''];
    }
```

## Brace Placement

The _Styler_ comes with several methods dedicated to brace placement.

- `braceOnNextLine()` puts an opening brace on the next line.
- `braceOnSameLine()` puts an opening brace on the same line.
- `braceEnd()` puts a closing brace on the same line.

Use these methods to place braces when overiding a `Styler::s*()` method.

In addition, the standard _Styler_ uses two common methods for brace placement on class-like structures and control-flow structures:

- `classBrace()` defines brace placement on class-like structures (`class`, `interface`, `trait`, etc.); defaults to `braceOnNextLine()`
- `controlBrace()` defines brace placement on control-flow structures (`if`, `do`, `foreach`, etc.); defaults to `braceOnSameLine()`

Override `classBrace()` to change brace placement on all class-like structures. Likewise, override `controlBrace()` to change brace placement on all control-flow structures. Finally, if you want to, you can override the class-like and control flow `Styler::s*()` methods to handle brace placement on each individual structure.

## Function Signatures

The default presentation behavior for a function signature with expansive parameters and a return typehint is to ...

- put a space on either side of the return typehint colon, and
- put the opening brace on the next line.

This keeps the parameters and return typehint lined up vertically with 4-space indents, and presents visual blank space between the signature and the body, like so:

```php
    function veryLongFunctionName(
        $veryLongParameter1,
        $veryLongParameter2,
        $veryLongParameter3,
        $veryLongParameter4,
        $veryLongParameter5,
    ) : ReturnTypeHint
    {
        // ...
    }
```

To change the spacing around the return typehint colon, override the method that adds the colon and spaces:

```php
    protected function sReturnType(P\ReturnType $p) : void
    {
        $this->line[] = ': ';
    }
```

To present the brace on the same line, override the method that sets the condition for when the space between the function signature and the function body should be clipped:

```php
    protected function functionBodyClipWhen(): callable
    {
        return fn (string $lastLine): bool => str_starts_with(trim($lastLine), ')');
    }
```

Changing the colon and brace placement in that manner will de-align the return typehint from the rest of the signature, and remove the visual blank space between the signature and the body:

```php
    function veryLongFunctionName(
        $veryLongParameter1,
        $veryLongParameter2,
        $veryLongParameter3,
        $veryLongParameter4,
        $veryLongParameter5,
    ): ReturnTypeHint {
        // ...
    }
```


## Trailing Comma

By default, the _Styler_ adds a trailing comma to the last item in an argument, parameter, or array listing, when that listing has been split across lines.

To *not* add that comma, override `Styler::lastSeparatorChar()` to return an empty string:

```php
    protected function lastSeparatorChar() : string
    {
        return '';
    }
```

## Finished Output

The finished output of styled code is handled by the `finish()` method. This is where you can add or trim lines around the code. For example, to make sure there is always a double-newline at the top of the file, and no line ending at the end:

```php
    protected function finish(string $code) : string
    {
        return '<?php' . $this->eol . $this->eol. trim($code);
    }
```
