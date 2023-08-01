# PHP Styler

**EXPERIMENTAL. NOT FOR PRODUCTION USE.**

PHP-Styler reconstructs PHP code after it has been deconstructed into an abstract syntax tree.

PHP-Styler is a companion to [PHP-Parser](https://github.com/nikic/PHP-Parser). Whereas the PHP-Parser pretty printer does not have output customization as a main design goal, PHP-Styler does.

Currently, PHP-Styler is targeted toward declaration/definition files (class, interface, enum, trait).

PHP-Styler is **not appropriate** for PHP-based templates, as it does not use the alternative control structures; perhaps a future release will include a custom _Styler_ for PHP-based templates.

## Design Goals

- **Logic Preservation.** Restructured PHP code will continue to operate as before.

- **Horizontal and Vertical Spacing.** Automatic, reasonable indenting and blank-line placement.

- **Line Length Control.** Automatic splitting across multiple lines when a single line is too long.

- **Diff-Friendly.** Default output should aid noise-reduction in diffs.

- **Customization.** Change the output style of printable elements by extending the _Styler_ and overriding the method for each _Printable_ you want to change.

- **Comment Preservation.** As much as the PHP-Parser will allow.

## Using PHP-Styler

Use `composer` to add PHP-Styler as a dev reequirement:

```
composer require --dev pmjones/php-styler
```

Then issue `php bin/styler.php` with the path to a source PHP file:

```
php bin/styler.php ./My/Source/File.php
```

Styler will output the restructured PHP source code.

(TBD: Apply restructuring on the file in-place.)

## How It Works

PHP-Styler uses a 3-pass system to reformat and style PHP code:

1. _PHPParser\Parser_ converts the code to an abstract syntax tree of _Node_ elements.
2. _PHPStyler\Printer_ flattens the _Node_ tree into a list of _Printable_ elements.
3. _PHPStyler\Styler_ converts each _Printable_ back into text; it applies horizontal spacing, vertical spacing, and line-splitting rules as it goes.

PHP-Styler will **completely reformat** the code it is given. If you like, think of PHP-Styler as the Genesis Device from "Star Trek II: The Wrath of Khan":

> McCoy: What if this thing were used where [formatting] already exists?
>
> Spock: It would destroy such [formatting] in favor of its new matrix.
>
> McCoy: Its new matrix? Do you have any idea what you're saying?
>
> Spock: I was not attempting to evaulate its moral implications.

However, the default styling is basically reasonable, and can be customized with little effort.

## Automatic Line-Splitting

At first, PHP-Styler builds each statement/instruction as a single line. If that line is "too long" (80 characters by default) the _Styler_ reconstructs the code by trying to split it across multiple lines. It does so by applying one or more rules in order:

- Function definition parameters are split at commas;
- Array elements are split at commas;
- Conditions are split at precedence-indicating parentheses, boolean operators, and ternary operators;
- Method calls are split at `->` and `?->` operators;
- Argument lists are split at commas.

If the first rule does not make the line short enough, the second rule is applied in addition, then the third, and so on.

Even after all rules are applied, the line may still end up "too long."

If a resulting line looks "ugly" or "weird" it may be an indication that it should be refactored.

## Caveats

(These are not all-inclusive.)

PHP-Styler does not:

- rearrange or reorder code blocks
- separate imports into groups (use, use function, use const)
- split comment lines

PHP-Styler will de-align lines like this ...

```
$foo = 'longish'    . $bar
$foo = 'short'      . $bar;
$foo = 'muchlonger' . $bar;
```

... into this:

```
$foo = 'longish' . $bar
$foo = 'short' . $bar;
$foo = 'muchlonger' . $bar;
```

Comment lines are always attached to the following line, not the same or previous line. That is, leading or trailing comments *on the same line* may not appear where you expect. Likewise, comments intended to be attached to the *previous* line may end up attached to the *following* line. (This is a limitation of PHP-Parser.)

## Comparable Offerings

[PHP CS Fixer](https://cs.symfony.com/) is the category leader here. It offers a huge range of customization options to fix (or not fix) specific elements of PHP code. However, it is extremely complex and difficult to modify. By comparison, PHP-Styler does not "fix" code; it restructures code entirely from an abstract syntax tree. It is also much less complex to modify.
