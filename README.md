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

You can try an online demonstration of PHP-Styler at <http://64.227.98.80/>.

* * *

## Introduction

PHP-Styler is a companion to [PHP-Parser](https://github.com/nikic/PHP-Parser) for reconstructing PHP code after it has been deconstructed into an abstract syntax tree.

Whereas the PHP-Parser pretty printer does not have output customization as a main design goal, PHP-Styler does. (Please review [README-CUSTOM.md](./README-CUSTOM.md) for more information.)

PHP-Styler is targeted toward declaration/definition files (class, interface, enum, trait) and script files.

PHP-Styler is **not appropriate** for PHP-based templates, as it does not use the alternative control structures. Perhaps a future release will include a custom _AlternativeStyler_ for PHP-based templates using alternative control structures.

### How It Works

PHP-Styler uses a 3-pass system to reformat and style PHP code:

1. _PHPParser\Parser_ converts the code to an abstract syntax tree of _Node_ elements.
2. _PHPStyler\Printer_ flattens the _Node_ tree into a list of _Printable_ elements.
3. _PHPStyler\Styler_ converts each _Printable_ back into text using a series of _Line_ objects; it applies horizontal spacing, vertical spacing, and line-splitting rules as it goes.

### Design Goals

- **Logic Preservation.** Restructured PHP code will continue to operate as before.

- **Horizontal and Vertical Spacing.** Automatic indenting and blank-line placement.

- **Line Length Control.** Automatic splitting across multiple lines when a single line is too long.

- **Diff-Friendly.** Default output should aid noise-reduction in diffs.

- **Customization.** Change the output style of printable elements by extending the _Styler_ and overriding the method for each _Printable_ you want to change.

- **Comment Preservation.** As much as the PHP-Parser will allow.

### Styling Examples

See the [Examples](./tests/Examples) directory for a nearly-exhaustive series of styling examples, or try the safe `preview` command on one of your own source files.

### Comparable Offerings

[PHP CS Fixer](https://cs.symfony.com/) is the category leader for PHP here. It offers a huge range of customization options to fix (or not fix) specific elements of PHP code. However, it is extremely complex, and can be difficult to modify.

Other PHP code fixers include [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)/[PHPCBF](https://phpqa.io/projects/phpcbf.html) and [ECS](https://github.com/easy-coding-standard/easy-coding-standard).

The [Black](https://black.readthedocs.io/en/stable/) formatter for Python appears to have similar design goals and operation as PHP-Styler.

Likewise, [dart_style](https://pub.dev/packages/dart_style) is a formatter for Dart. (Read more about how it works [here](https://journal.stuffwithstuff.com/2015/09/08/the-hardest-program-ive-ever-written/).)

Finally, there is a [PHP plugin for Prettier](https://github.com/prettier/plugin-php) that uses JavaScript to replace all PHP code formatting using its own rules.


## Usage

### Installation

Use `composer` to add PHP-Styler as a dev requirement:

```
composer require --dev pmjones/php-styler 0.x-dev
```

Copy the default `php-styler.php` config file to your package root:

```
cp ./vendor/pmjones/php-styler/resources/php-styler.dist.php ./php-styler.php
```

### Preview Formatting

Safely preview how PHP-Styler will restructure a source PHP file:

```
./vendor/bin/php-styler preview ./src/My/Source/File.php
```

Pass `-c` or `--config` to specify an alternative config file:

```
./vendor/bin/php-styler preview \
    -c /path/to/other/php-styler.php \
    ./src/My/Source/File.php
```

Pass `--debug-parser` to dump the PHP-Parser AST _Node_ objects into the preview, and/or `--debug-printer` to dump the PHP-Styler array of _Printable_ objects into the preview.

### Apply Formatting

Apply PHP-Styler to all files identified in the config file, overwriting them with new formatting:

```
./vendor/bin/php-styler apply
```

Pass `-c` or `--config` to specify an alternative config file:

```
./vendor/bin/php-styler apply -c /path/to/other/php-styler.php
```

PHP-Styler will only apply formatting to files with a modification time *later* than the cache file. To force formatting on all files regardless of modification time, pass the `--force` option:

```
./vendor/bin/php-styler apply --force
```

Changing the config file after `apply` will invalidate the cache, implying `--force` and thereby causing PHP-Styler to apply formatting to all files.


### Check Formatting

Check all files identified in the config file to see if they need formatting, without changing any of the files:

```
./vendor/bin/php-styler check
```

Pass `-c` or `--config` to specify an alternative config file:

```
./vendor/bin/php-styler apply -c /path/to/other/php-styler.php
```

If all files look OK, the return code is `0`. If one or more files look like they need to be styled, the return code is `1`.


### Configuration

The default `php-styler.php` config file looks like this:

```php
<?php
use PhpStyler\Config;
use PhpStyler\Files;
use PhpStyler\Styler;

return new Config(
    files: new Files(__DIR__ . '/src'),
    styler: new Styler(),
    cache: __DIR__ . '/.php-styler.cache',
);
```

- `iterable $files` is any `iterable` of file names to which PHP-Styler should be applied. (If the PHP-Styler _Files_ object is not sufficient for your purposes, try [Symfony Finder](https://symfony.com/doc/current/components/finder.html) instead.)

- `Styler $styler` is any instance of _Styler_, whether the default one or any custom extended class.

- `?string $cache` is the name of the cache file; the last-modified time of this file indicates the last time PHP-Styler was applied. If `$cache` is null then no caching will be used.

The _Styler_ instance can be configured with these constructor parameters:

- `string $eol = "\n"`: The end-of-line string to use.

- `int $lineLen = 88`: The maximum line length before PHP-Styler tries to split lines automatically.

- `int $indentLen = 4`: The indent length in spaces.

- `bool $indentTab = false`: When `true`, use a tab (`"\t"`) for indenting instead of spaces; `$indentLen` is used as the tab width when calculating line length.

Here is a _Styler_ configured for Windows line endings on 120-character lines with tab indentation at 8 spaces wide:

```php
<?php
use PhpStyler\Config;
use PhpStyler\Files;
use PhpStyler\Styler;

return new Config(
    files: new Files(__DIR__ . '\\src'),
    styler: new Styler(
        eol: "\r\n",
        lineLen: 120,
        indentLen: 8,
        indentTab: true,
    ),
);
```

### Avoiding Blame

Applying PHP-Styler to your source files for the first time may introduce a volume of changes that will make it difficult to track authorship via `git blame`.

You can tell Git to overlook this initial reformatting pass by adding a `.git-blame-ignore-revs` file to your repository, and adding the full hash of the initial reformatting commit to it.

1. Issue `php-styler apply` to your codebase and commit the changes.
2. Issue `git log` and copy the full 40-character hash string from that commit.
3. Create and commit a file named `.git-blame-ignore-revs` with that hash pasted into it, perhaps with a comment.
4. Configure Git to look at that file: `git config blame.ignoreRevsFile .git-blame-ignore-revs`

Voila: `git blame` will now ignore that file when looking at authorship history, as will the GitHub `blame` user interface.

(See also <https://git-scm.com/docs/git-blame#Documentation/git-blame.txt---ignore-revs-fileltfilegt>.)

### Line Splitting

#### Automatic

At first, PHP-Styler builds each statement/instruction as a single line. If that line is "too long" (88 characters by default) the _Styler_ reconstructs the code by trying to split it across multiple lines. It does so by applying one or more rules in order:

- `implements` are split at commas.
- Arrow functions are split at `=>`.
- String concatenations are split at dots.
- Conditions are split at parentheses.
- Precedence-indicating parentheses are split.
- Ternaries are split at `?`, `:`, and `?:`.
- Boolean `||` and logical `or` operators are split.
- Boolean `&&` and logical `and` operators are split.
- Array elements are split at commas.
- Argument lists are split at commas.
- Coalesce `??` operators are split.
- Member operators are split at `::`, `::$`, `->` and `?->`.
- Parameter lists are split at commas.

If the first rule does not make the line short enough, the second rule is applied in addition, then the third, and so on.

The line splitting logic attempts to be idiomatic; that is, PHP-Styler tries to take common line-splitting idioms into account, rather than making weighted calculations of elements. Reference projects were:

- cakephp/database
- laminas/laminas-mvc
- nette/application
- qiq/qiq
- sapien/sapien
- slim/slim
- symfony/http-foundation


#### Annotated

Sometimes you may want to force lines to split expansively across lines. For example, a deeply-nested array with many elements per nesting level may look better when every element is on its own line, regardless of how short that element may be.

To force expansiveness of line splitting, add the annotation `@php-styler-expansive` above the line in question. For example, this array ...

```php
$foo = ['bar', 'baz', 'dib'];
```

... would normally be presented on a single line. However, when adding the `@php-styler-expansive` annotation ...

```php
/** @php-styler-expansive */
$foo = [
    'bar',
    'baz',
    'dib',
];
```

... the elements are made to split expansively across lines.

PHP-Styler recognizes the one-liner annotations `/** @php-styler-expansive */` and `// @php-styler-expansive`, as well as typical docblock annotations:

```php
/**
 * @php-styler-expansive
 */
```

### Fixing Mangled Output

If PHP-Styler generates "ugly" or "weird" or "mangled" results, it might be a problem with how PHP-Styler works; please submit an issue.

Alternatively, it may be an indication that the source line(s) should be refactored. Here are some suggestions:

- Increase the maximum line length. The default length is 88 characters (10% more than the commonly-suggested 80-character length to allow some wiggle room). However, some codebases tend to prefer much longer lines, so increasing the line length may result in more-agreeable line splits.

- Remove comments from within parameter and argument lists.

- Move inline comments from the beginning or end of the line to *above* the line.

- Break up a single long line into multiple shorter lines.

- Assign closures embedded in arguments to separate variables.

- Assign function calls embedded in concatenations to separate variables.

- Assign multiple ternaries embedded in a single statement to separate variables.

Unfortunately, because of how PHP-Parser handles double-quoted strings with interpolated variables ("encapsed" strings), newlines and some other whitespace characters (`\f`, `\r`, `\t`, `\v`) render as a literal `\n` (etc.) within the string. For example, this code ...

```php
$sql = "
    SELECT *
    FROM {$table}
";
```

... will be rendered as ...

```php
$sql = "\n    SELECT TABLE_NAME\n    FROM {$table}\n";
```

... which is not what I would expect to see.

Until there is a change to how PHP-Parser works, the only solution I can think of is to use heredoc syntax instead. Then this code ...

```php
$sql = <<<SQL
    SELECT *
    FROM {$table}
SQL;
```
... will be rendered exactly as provided.


## Caveats

These are not all-inclusive; see also [FIXME.md](./FIXME.md) for known issues to be addressed.

### Line Length

Even after all line splitting rules are applied, a line may still end up "too long." For example, if a line has a very long quoted string, PHP-Styler cannot split it for you.

### Reordering Code

PHP-Styler does not:

- Regroup `use` imports
- Split comment lines
- Split quoted strings, heredocs, or nowdocs

### Horizontal Alignment

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

### Vertical Spacing

PHP-Styler will compress lines like this ...

```
$foo = 'longish' . $bar

$foo = 'short' . $bar;

$foo = 'muchlonger' . $bar;
```

... into this:

```
$foo = 'longish' . $bar
$foo = 'short' . $bar;
$foo = 'muchlonger' . $bar;
```

If you want extra vertical spacing, add a comment; comment lines get one blank line above them.

```
// baseline foo
$foo = 'longish' . $bar

// reassign foo
$foo = 'short' . $bar;

// reassign foo again
$foo = 'muchlonger' . $bar;
```

### Comment Lines

PHP-Styler does not reformat comment line contents.

Comment lines are always attached to the following line, not the same or previous line. That is, leading or trailing comments *on the same line* may not appear where you expect. Likewise, comments intended to be attached to the *previous* line may end up attached to the *following* line. (This is a limitation of PHP-Parser.)

Comments within argument or parameter lists may mess up indenting; consider removing them.

Inline comments within array elements may mess up indenting; consider placing the comment on the line above that element instead.

Comments on closure signatures may mess up indenting. For example, the following is how PHP-Styler reformats one part of Laminas Escaper:

```php
$this->htmlAttrMatcher =

/** @param array<array-key, string> $matches */
function (array $matches) : string {
   return $this->htmlAttrMatcher($matches);
};
```
