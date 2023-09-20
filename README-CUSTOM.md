# Customizing PHP-Styler

## Overview

1. [Custom _Styler_ class](#custom-styler-class)

2. [Method overrides](#method-overrides)

3. [Operator spacing](#operator-spacing)

4. [Brace placement](#brace-placement)

5. [Finished output](#finished-output)

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
- `controlBrace()` defines brace placement on control-flow structures ((`if`, `do`, `foreach`, etc.); defaults to `braceOnSameLine()`

Override `classBrace()` to change brace placement on all class-like structures. Likewise, override `controlBrace()` to change brace placement on all control-flow structures. Finally, if you want to, you can override the class-like and control flow `Styler::s*()` methods to handle brace placement on each individual structure.

## Finished Output

The finished output of styled code is handled by the `finish()` method. This is where you can add or trim lines around the code. For example, to make sure there is always a double-newline at the top of the file, and no line ending at the end:

```php
    protected function finish(string $code) : string
    {
        return '<?php' . $this->eol . $this->eol. trim($code);
    }
```
