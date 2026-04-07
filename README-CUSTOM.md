# Customizing PHP-Styler

## Overview

All customization in PHP-Styler is declarative through *Format* objects. A
Format defines three arrays — *Styles*, *Rules*, and *Parses* — along with
basic layout settings (`eol`, `lineLen`, `indentLen`, `indentTab`).

There are two approaches to customization:

1. [Constructor Parameters](#constructor-parameters) — pass values to an
   existing Format class.

2. [Extending a Format Class](#extending-a-format-class) — create a subclass
   with your own defaults.


## Constructor Parameters

### Basic Layout

All Format classes accept these constructor parameters:

```php
use PhpStyler\Config;
use PhpStyler\Files;
use PhpStyler\Format\PlainFormat;

return new Config(
    files: new Files(__DIR__ . '/src'),
    format: new PlainFormat(
        lineLen: 120,
        indentLen: 2,
        indentTab: true,
    ),
);
```

### Common Adjustments

`PlainFormat` provides constructor parameters for frequently customized
behavior. `DeclarationFormat` hardcodes these to opinionated defaults (shown
below) and does not accept them as constructor parameters; to customize them,
extend `DeclarationFormat` and call the protected setter methods (see
[Extending a Format Class](#extending-a-format-class)).

| Parameter | Default (Plain) | Default (Declaration) | Values |
|---|---|---|---|
| `classBracePosition` | `'same_line'` | `'next_line'` | `'same_line'`, `'next_line'` |
| `functionBracePosition` | `'same_line'` | `'next_line'` | `'same_line'`, `'next_line'` |
| `controlBracePosition` | `'same_line'` | `'same_line'` | `'same_line'`, `'next_line'` |
| `keywordCase` | `'lower'` | `'lower'` | `'lower'`, `'upper'` |
| `concatenationSpacing` | `true` | `true` | `true` (spaces around `.`), `false` (no spaces) |
| `returnTypeColonSpacing` | `true` | `true` | `true` (` : Type`), `false` (`: Type`) |
| `blankLineAfterBlock` | `false` | `true` | `true` (blank line after closing braces), `false` |

```php
use PhpStyler\Format\PlainFormat;

return new Config(
    files: new Files(__DIR__ . '/src'),
    format: new PlainFormat(
        lineLen: 120,
        controlBracePosition: 'next_line',
        concatenationSpacing: false,
        returnTypeColonSpacing: false,
    ),
);
```

### Styles

The `styles` array controls per-token presentation: spacing, line breaks, blank
lines, and casing. Each entry maps a token class name to an array of style
arguments:

```php
use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Token;

return new Config(
    files: new Files(__DIR__ . '/src'),
    format: new DeclarationFormat(
        styles: [
            // blank line before every return statement
            Token\TReturn::class => ['blankLineBefore' => true],
            // no space after casts
            Token\TIntCast::class => ['spaceAfter' => false],
            // lowercase function call names
            Token\TFunctionCallName::class => ['case' => 'strtolower'],
        ],
    ),
);
```

The available style arguments are:

| Argument | Type | Effect |
|---|---|---|
| `spaceBefore` | `?bool` | Space before the token (`null` to clear) |
| `spaceAfter` | `?bool` | Space after the token |
| `lineBreakBefore` | `?bool` | Line break before the token |
| `lineBreakAfter` | `?bool` | Line break after the token |
| `blankLineBefore` | `?bool` | Blank line before the token |
| `blankLineAfter` | `?bool` | Blank line after the token |
| `case` | `?callable` | Case conversion (e.g. `'strtolower'`, `'strtoupper'`) |

Passing `null` for a value clears any existing setting for that argument.

Styles passed via the constructor are merged with the Format's defaults — your
values override on a per-argument basis without discarding the rest of the
token's style.

#### Blank line denial around split lines

When the Splitter breaks a long line into multiple lines, it inserts blank lines
above and below the split group. The `blankLineBefore` and `blankLineAfter`
style arguments can suppress this insertion when set to `false`:

- `blankLineAfter => false` on a token prevents a blank line after a line ending
  with that token at a split boundary.
- `blankLineBefore => false` on a token prevents a blank line before a line
  starting with that token at a split boundary.

These denial values are only checked by the Splitter's blank-line logic — they
do not affect blank lines emitted by the Parser (which uses `true` to *add*
blank lines). The defaults deny blank lines at block boundaries (after opening
braces, before closing braces), after comments and docblocks, after attribute
brackets, and before opening braces configured via brace position settings.

### Rules

The `rules` array controls structural transformations. Each entry maps a rule
class name to its constructor arguments (an empty array for rules with no
parameters):

```php
use PhpStyler\Format\PlainFormat;
use PhpStyler\Rule\TokenRule;
use PhpStyler\Token;

return new Config(
    files: new Files(__DIR__ . '/src'),
    format: new PlainFormat(
        rules: [
            TokenRule\NormalizeImports::class => [],
            TokenRule\NormalizeTypeOrder::class => ['order' => ['*', Token\TNull::class]],
        ],
    ),
);
```

Rules passed via the constructor are merged with the Format's existing rules.
If a rule class already exists in the Format's defaults, the passed arguments
replace its arguments.

Available rules:

| Rule | Effect |
|---|---|
| `CollapseEmptyBody` | Collapse empty method/function bodies to `{}` on one line |
| `ConvertFromYodaConditions` | Convert `null === $x` to `$x === null` |
| `ConvertToShortArraySyntax` | Convert `array()` to `[]` |
| `ConvertToShortListSyntax` | Convert `list()` to `[]` |
| `ConvertToYodaConditions` | Convert `$x === null` to `null === $x` |
| `ConvertVarToPublic` | Convert `var` to `public` |
| `ExpandConstDeclarations` | Expand `const A = 1, B = 2` into separate declarations |
| `ExpandGroupedImports` | Expand `use Foo\{Bar, Baz}` into individual statements |
| `ExpandPropertyDeclarations` | Expand `public int $a, $b` into separate declarations |
| `InsertNewParens` | Add `()` to bare `new Foo` |
| `InsertPublicVisibility` | Add `public` to class functions/consts without visibility |
| `MergeParenBrace` | Merge closing paren and opening brace onto one line |
| `MergeParenBracket` | Merge `])` onto the same line |
| `NormalizeImports` | Remove unused and sort `use` statements |
| `NormalizeMemberOrder` | Reorder class members by type; accepts `order` parameter |
| `NormalizeMemberSpacing` | Normalize blank lines between class members |
| `NormalizeModifierOrder` | Sort modifiers by priority (abstract/final, visibility, static, readonly) |
| `NormalizeTrailingCommas` | Normalize trailing commas in split lists |
| `NormalizeTypeOrder` | Sort union/intersection types; accepts `order` parameter |
| `RemoveBom` | Remove UTF-8 byte-order mark |
| `RemoveEmptyAnonymousClassParens` | Remove empty `()` from `new class()` |
| `RemoveEmptyAttributeParens` | Remove empty `()` from `#[Attr()]` |
| `RemoveLanguageConstructParens` | Remove optional parens from `echo()`, `print()`, etc. |
| `RemovePhpClosingTag` | Remove trailing `?>` |
| `RemoveRepeatedSemicolons` | Remove duplicate `;;` |
| `RemoveTrailingBlankLines` | Remove trailing blank lines from blocks |

### Parse As

The `parseAs` array substitutes one token class for another during parsing,
changing how certain constructs are interpreted and rendered:

```php
use PhpStyler\Format\PlainFormat;
use PhpStyler\Token;

return new Config(
    files: new Files(__DIR__ . '/src'),
    format: new PlainFormat(
        parseAs: [
            // convert array() to []
            Token\TArray::class => Token\TArrayAsShort::class,
            // convert double-quoted strings to single-quoted
            Token\TStringLiteral::class => Token\TStringLiteralAsSingleQuote::class,
            // convert else-if to elseif
            Token\TElse::class => Token\TElseAsElseIf::class,
        ],
    ),
);
```

Available parse substitutions:

| From | To | Effect |
|---|---|---|
| `TArray` | `TArrayAsShort` | `array()` to `[]` |
| `TContinue` | `TContinueAsBreak` | `continue` to `break` in switches |
| `TElse` | `TElseAsElseIf` | `else if` to `elseif` |
| `THeredocStart` | `THeredocStartAsNowdoc` | Heredocs to nowdocs |
| `TList` | `TListAsArray` | `list()` to `[]` |
| `TLogicalAnd` | `TLogicalAndAsBooleanAnd` | `and` to `&&` |
| `TLogicalOr` | `TLogicalOrAsBooleanOr` | `or` to `\|\|` |
| `TPhpClosingTag` | `TPhpClosingTagRemoved` | Remove `?>` closing tags |
| `TSemicolon` | `TSemicolonSkipRepeats` | Remove repeated semicolons |
| `TStringLiteral` | `TStringLiteralAsSingleQuote` | Double-quoted to single-quoted strings |
| `TVariable` | `TVariableWithExplicitInterpolation` | Explicit `{$var}` interpolation |


## Extending a Format Class

For reusable configurations, extend a Format class. Override the `$styles`,
`$rules`, and `$parseAs` properties, and call protected setter methods in your
constructor.

Here is an example that extends `DeclarationFormat`:

```php
<?php
declare(strict_types=1);

namespace My\Project;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule;
use PhpStyler\Rule\TokenRule;
use PhpStyler\Token;

class MyFormat extends DeclarationFormat
{
    /**
     * @inheritdoc
     */
    public protected(set) array $parseAs = [
        Token\TElse::class => Token\TElseAsElseIf::class,
        Token\TVariable::class => Token\TVariableWithExplicitInterpolation::class,
    ];

    /**
     * @inheritdoc
     */
    public protected(set) array $rules = [
        TokenRule\RemoveBom::class => [],
        TokenRule\NormalizeImports::class => [],
        TokenRule\NormalizeTypeOrder::class => [],
        TokenRule\MergeParenBracket::class => [],
        LineRule\MergeParenBrace::class => [],
        LineRule\NormalizeMemberSpacing::class => [],
        LineRule\NormalizeTrailingCommas::class => [],
        LineRule\RemoveTrailingBlankLines::class => [],
    ];

    /**
     * @inheritdoc
     */
    public function __construct(
        string $eol = "\n",
        int $lineLen = 120,
        int $indentLen = 4,
        bool $indentTab = false,
        array $styles = [],
        array $rules = [],
        array $parseAs = [],
    ) {
        // styles specific to this format
        $myStyles = [
            Token\TReturn::class => ['blankLineBefore' => true],
            Token\TIntCast::class => ['spaceAfter' => true],
            Token\TBoolCast::class => ['spaceAfter' => true],
        ];

        // merge in caller-provided styles
        foreach ($styles as $class => $args) {
            $myStyles[$class] = array_merge(
                $myStyles[$class] ?? [],
                $args,
            );
        }

        parent::__construct(
            eol: $eol,
            lineLen: $lineLen,
            indentLen: $indentLen,
            indentTab: $indentTab,
            styles: $myStyles,
            rules: $rules,
            parseAs: $parseAs,
        );

        // post-construction adjustments
        $this->setConcatenationSpacing(false);
        $this->setReturnTypeColonSpacing(false);
    }
}
```

Then use it in your config file:

```php
<?php
use My\Project\MyFormat;
use PhpStyler\Config;
use PhpStyler\Files;

return new Config(
    files: new Files(__DIR__ . '/src'),
    format: new MyFormat(),
);
```

### Property Overrides

When you declare `$parseAs`, `$rules`, or `$styles` as a property on your
subclass, it *completely replaces* the parent's property — it is not merged.
Build the full list you want.

### Constructor Pattern

The constructor pattern used by the vendor formats is:

1. Define format-specific styles in a local array.
2. Merge in any caller-provided `$styles` so users of your format can still
   override individual tokens.
3. Call `parent::__construct()` with the merged styles and other parameters.
4. Call protected setter methods (`setConcatenationSpacing()`,
   `setReturnTypeColonSpacing()`, etc.) for post-construction adjustments.

### Available Setter Methods

These protected methods modify the styles array after construction:

| Method | Effect |
|---|---|
| `setClassBracePosition('same_line'\|'next_line')` | Brace position for classes, interfaces, traits, enums |
| `setFunctionBracePosition('same_line'\|'next_line')` | Brace position for functions/methods |
| `setControlBracePosition('same_line'\|'next_line')` | Brace position for if, do, foreach, etc. |
| `setKeywordCase('lower'\|'upper')` | Case for `array`, `null`, `true`, `false` |
| `setConcatenationSpacing(bool)` | Spaces around the `.` operator |
| `setReturnTypeColonSpacing(bool)` | Space before return type colon (` : Type` vs `: Type`) |
| `setBlankLineAfterBlock(bool)` | Blank line after closing braces and semicolons |


## Discovering Token Classes

To find the token class name for a specific PHP construct, look in the
`src/Token/` directory. Token classes are named after the construct they
represent (e.g., `TReturn`, `TAssign`, `TIfOpeningBrace`,
`TClassClosingBrace`).

Use the `preview` command to see how PHP-Styler formats a file, then look at
the token classes in the source to identify which ones to style.
