# PHP-Styler Architecture: Old vs. New

This document compares the 0.16.0 architecture (AST-based, using nikic/php-parser)
with the current architecture (token-based, using PHP's built-in `PhpToken` lexer).

## Old Architecture (0.16.0)

### Dependencies

- `php`: ^8.1 | ^8.2 | ^8.3
- `nikic/php-parser`: ^4.19
- `pmjones/auto-shell`: ^1.0

### Pipeline

```
Source Code
    │
    ▼
Parser (extends PhpParser\Parser\Php7)
    │  pre-processes: converts `else if` to `elseif`
    ▼
Abstract Syntax Tree (PhpParser\Node objects)
    │
    ▼
Visitor (extends PhpParser\NodeVisitorAbstract)
    │  attaches metadata: fluency tracking, expansive annotations
    ▼
Annotated AST
    │
    ▼
Printer (src/Printer.php, ~40 KB)
    │  flattens AST into Printable objects
    │  ~150 methods, one per Node type (pArg, pArgs, pClass_, etc.)
    ▼
Array of Printable objects (89 classes in src/Printable/)
    │
    ▼
Styler (src/Styler.php)
    │  converts Printables to Line objects
    │  applies horizontal/vertical spacing and line splitting
    ▼
Array of Line objects
    │
    ▼
Rendered output
```

### Key Classes

| Class | Responsibility |
|---|---|
| `Parser` | Extends nikic/php-parser; pre-processes `else if` |
| `Visitor` | AST node visitor; tracks fluency and expansive annotations |
| `Printer` | Flattens AST nodes into 89 Printable types |
| `Styler` | Converts Printables to Lines; spacing, splitting, rendering |
| `Line` | A single line of output being assembled |
| `Split` | Line-splitting logic |
| `Nesting` | Tracks nesting depth for indentation |
| `Whitespace`, `Whitespace\Condense`, `Whitespace\Rtrim` | Whitespace helpers |

### Printable Classes (89)

Each `Printable` subclass represented a single AST-related construct. Examples:
`Args`, `Array_`, `ArrowFunction`, `Body`, `Cast`, `Class_`, `ClassConst`,
`ClassMethod`, `ClassProperty`, `Closure`, `Cond`, `Declare_`, `Do_`, `Else_`,
`Encapsed`, `Enum_`, `Expr`, `For_`, `Foreach_`, `Function_`, `If_`,
`Implements_`, `Interface_`, `Match_`, `MatchArm`, `Namespace_`, `Return_`,
`Switch_`, `TryCatch`, `While_`, etc.

### Customization

Users extended the `Styler` class and overrode methods:

- **`s*()` methods** — one per Printable type (e.g., `sClassConst()`,
  `sFunction_()`, `sIf_()`). Each method controlled how that construct was
  styled.
- **`classBrace()` / `controlBrace()`** — brace placement on class-like and
  control structures.
- **`modOperators()`** — operator spacing as `[space, operator, space]` arrays
  keyed by `PhpParser\Node\Expr` class names.
- **`lastSeparator()` / `last*Separator()`** — trailing comma behavior.
- **`sReturnType()`** — return type colon spacing.
- **`functionBodyCondenseWhen()`** — when to put opening brace on same line.

### Limitations

- **Comment loss.** nikic/php-parser could lose comments in certain positions
  (inside argument lists, end of arrays, sole content of blocks, concatenation
  lines).
- **Interpolated string mangling.** The AST reconstructed double-quoted strings,
  converting literal newlines to `\n` escapes.
- **No vertical spacing preservation.** All blank lines were compressed.
- **Monolithic customization.** Changing one aspect (e.g., trailing commas)
  required subclassing the entire Styler.

* * *


## New Architecture (0.17 and after)

### Dependencies

- `php`: >=8.4
- `pmjones/auto-shell`: ^1.0

nikic/php-parser has been eliminated entirely.

### Pipeline

```
Source Code
    │
    ▼
PhpToken::tokenize()
    │  PHP's built-in lexer
    ▼
Array of PhpToken objects
    │
    ▼
Parser (src/Parser.php)
    │  maps each PhpToken to an AToken subclass
    │  invokes AToken::parse() for context-specific parsing
    │  applies Styles (spacing, line breaks, casing) from Format
    │  injects synthetic tokens (braces, parens, splits)
    ▼
Array of AToken objects (~520 classes in src/Token/)
    │
    ▼
Token Rules (src/Rule/TokenRule.php implementations)
    │  structural transformations on the token stream
    │  e.g., NormalizeImports, OrderTypes, NormalizeTrailingCommas
    ▼
Transformed token stream
    │
    ▼
Assembler (src/Assembler.php)
    │  groups tokens into Line objects by tracking line breaks and indentation
    ▼
Array of Line objects
    │
    ▼
Splitter (src/Splitter.php)
    │  checks each line against max length
    │  splits at TSplit points in priority order
    │  expands opener/closer pairs across lines
    │  inserts blank lines around split groups (with style-based denial)
    ▼
Split lines
    │
    ▼
Line Rules (src/Rule/LineRule.php implementations)
    │  final adjustments on assembled lines
    │  e.g., RemoveTrailingBlankLines, RejoinOrphans
    ▼
Final lines
    │
    ▼
Rendered output
```

### Key Classes

| Class | Responsibility |
|---|---|
| `Parser` | Maps PhpTokens to AToken subclasses; applies styles; injects synthetic tokens |
| `AToken` | Abstract base for all 507 token classes; extends `PhpToken`; carries its `Style` instance |
| `Style` | Per-token spacing, line breaks, blank lines, casing (`readonly`) |
| `Format` (interface) | Declares `eol`, `lineLen`, `indentLen`, `indentTab`, `parseAs`, `styles`, `rules` |
| `PlainFormat` | Base format with styles for all token types |
| `DeclarationFormat` | Extends PlainFormat with opinionated rules and parseAs |
| `Styler` | Pipeline orchestrator: parse → tokenRules → assemble → split → lineRules → render |
| `Assembler` | Converts token stream to Line objects |
| `Splitter` | Splits long lines at priority-ordered split points; inserts blank lines around split groups |
| `Line` | A single output line; holds AToken array and indent level |
| `LineFactory` | Creates Line instances with configured dimensions |
| `Nesting` | Tracks nesting context for tokens |
| `Docblock` / `DocblockTag` | Docblock structure parsing |

### Token System (507 classes)

Each PHP token and synthetic construct has a dedicated `AToken` subclass.

**Base hierarchy:**

- `AToken` (abstract) — extends `PhpToken`; adds `parenDepth`, `argCount`,
  `openingToken`, `closingToken`, `parse()`, `render()`, `splitBefore()`,
  `splitAfter()`, `isOpener()`, `isContent()`, `expandPriority()`,
  `splObjectId()`
- `ALanguageConstruct` — abstract base for keyword tokens
- `TSplit` (abstract) — base for split-point markers

**Categories:**

- **Keywords:** `TAbstract`, `TClass`, `TFunction`, `TIf`, `TElse`, `TReturn`,
  `TMatch`, `TNew`, etc.
- **Operators:** `TAssign`, `TBooleanAnd`, `TBooleanOr`, `TConcat`,
  `TIdentical`, `TTernaryThen`, `TTernaryElse`, etc.
- **Delimiters:** `TOpeningParen`, `TClosingParen`, `TOpeningBrace`,
  `TClosingBrace`, `TOpeningBracket`, `TClosingBracket`, `TComma`,
  `TSemicolon`, etc. With context variants: `TIfOpeningBrace`,
  `TClassClosingBrace`, `TFunctionOpeningParen`, etc.
- **Literals:** `TStringLiteral`, `TInteger`, `TFloat`, `THeredocStart`,
  `THeredocEnd`, etc.
- **Synthetic:** `TSpace`, `TLineBreak`, `TBlankLine`, `TIndentIncrement`,
  `TIndentDecrement` — injected by the Parser, not from source.
- **Split markers:** `TSplitComma`, `TSplitTernary`, `TSplitCoalesce`,
  `TSplitBooleanOr`, `TSplitBooleanAnd`, `TSplitComparison`,
  `TSplitAddition`, `TSplitMultiplication`, `TSplitFluent`,
  `TSplitFnDoubleArrow`, `TSplitMatchDoubleArrow`, `TSplitAttribute`,
  `TSplitForSemicolon`, etc.
- **Parse-as variants:** `TArrayAsShort`, `TElseAsElseIf`,
  `TStringLiteralAsSingleQuote`, `THeredocStartAsNowdoc`,
  `TLogicalAndAsBooleanAnd`, etc.

**Split and expansion priority constants** (on `TSplittable`):

| Priority | Constant | Splits at |
|---|---|---|
| 10 | `CONDITION_PAREN` | `if`/`while`/`for`/`foreach`/`switch`/`match` paren expansion |
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
| 170 | `OTHER_PAREN` | Args, params, expression paren expansion |
| 180 | `ELEMENT_BRACKET` | Array element access `$arr[...]` expansion |

### Parser Detail

The Parser (`src/Parser.php`) is the most complex class. Key mechanics:

1. **Token mapping.** Each `PhpToken` is mapped to an `AToken` class via a
   large `TOKEN_CLASS` constant array plus auto-naming conventions (e.g.,
   `T_IF` maps to `TIf`, `T_ABSTRACT` maps to `TAbstract`).

2. **Parse-as substitution.** If the `Format::$parseAs` array maps a token class
   to another, the substitute class is used instead (e.g., `TArray` →
   `TArrayAsShort` converts `array()` to `[]`).

3. **Token parsing.** Each `AToken` subclass implements a static `parse()` method
   that receives the Parser instance. The parse method can:
   - Call `$parser->add()` to emit the token with its style
   - Call `$parser->addSplit()` to emit a split marker
   - Call `$parser->indentIncr()` / `indentDecr()` to manage indentation
   - Call `$parser->lineBreak()` / `space()` / `blankLine()` for spacing
   - Call `$parser->splice()` to inject synthetic tokens into the source stream
     (e.g., adding braces to braceless control structures)

4. **Style application.** When `add()` is called, the Parser looks up the token
   class in `Format::$styles`, creates a `Style` instance, attaches it to the
   token (`$token->style`), and applies `spaceBefore`, `spaceAfter`,
   `lineBreakBefore`, `lineBreakAfter`, `blankLineBefore`, `blankLineAfter`,
   and `case` transformation. The attached style is later used by the Splitter
   for blank-line denial checks.

### Format System

The `Format` interface defines:

- `$eol`, `$lineLen`, `$indentLen`, `$indentTab` — layout settings
- `$parseAs` — token-class substitution map
- `$styles` — token-class → style-args map (spacing, line breaks, casing)
- `$rules` — rule-class → constructor-args map

**`PlainFormat`** defines styles for all 507 token types with sensible defaults.
Constructor parameters expose common adjustments: `classBracePosition`,
`functionBracePosition`, `controlBracePosition`, `keywordCase`,
`concatenationSpacing`, `returnTypeColonSpacing`, `blankLineAfterBlock`.

**`DeclarationFormat`** extends `PlainFormat` with:
- `$parseAs`: array→short, list→short, else-if→elseif, remove closing tag,
  skip repeated semicolons, explicit variable interpolation
- `$rules`: RemoveBom, NormalizeImports, OrderTypes, MergeParenBracket,
  RejoinOrphans, NormalizeTrailingCommas, RemoveTrailingBlankLines
- Defaults: next-line class/function braces, same-line control braces,
  blank-line-after-block

**Vendor formats** (`Percs30Format`, `SymfonyFormat`, `DoctrineFormat`) extend
`DeclarationFormat` with coding-standard-specific parseAs, rules, and styles.

### Rule System

Rules implement one of two interfaces:

```php
interface TokenRule {
    public function apply(array $tokens): array;
}

interface LineRule {
    public function apply(array $lines): array;
}
```

Token rules operate on the `AToken[]` array after parsing, before assembly.
Line rules operate on the `Line[]` array after splitting.

The `Format::$rules` array specifies which rules to instantiate and their
constructor arguments. The `Styler` instantiates them at construction time and
applies them in declared order.

**Token rules:** RemoveBom, NormalizeImports, OrderTypes, CollapseEmptyBody,
ConvertToYodaConditions, ConvertFromYodaConditions, NormalizeMemberSpacing.

**Line rules:** MergeParenBracket, RejoinOrphans, NormalizeTrailingCommas,
RemoveTrailingBlankLines.

### Assembler

The `Assembler` converts the styled token stream into `Line` objects:

- Tracks current indent level (starting at 0).
- `TIndentIncrement` / `TIndentDecrement` tokens adjust the indent.
- `TLineBreak` tokens flush the current line and start a new one.
- All other tokens are appended to the current line.

### Splitter

The `Splitter` handles line-length enforcement and blank line insertion using a
unified priority-ordered strategy system:

1. For each line, check if it exceeds `lineLen` (or has an interior comment
   forcing expansion).
2. Collect all split strategies into a flat map keyed by priority:
   - **Operator splits** from `TSplit` tokens (via `Line::collectSplitGroups()`).
   - **Expansion splits** from opener tokens that declare `expandPriority()`
     (via `Line::collectExpansionPairs()`). Opener tokens like
     `TIfOpeningParen`, `TArrayOpeningBracket`, and `TArgsOpeningParen`
     declare their own expansion priority polymorphically.
3. Sort strategies by priority (`ksort`) and try each in order. The first
   strategy that produces a valid split wins.
4. Recursively split any resulting lines that are still too long.
5. Post-split passes: expand opener/closer pairs across lines, normalize
   indents, expand commas in multi-line constructs.
6. Insert blank lines around split groups. Lines produced from splitting a
   single original line form a "split group." Blank lines are inserted at
   transitions between split and non-split lines, unless the adjacent token's
   style has `blankLineBefore => false` or `blankLineAfter => false` (denial).
7. Detect `@php-styler-expansive` annotations and force expansion.

* * *

## Key Architectural Shifts

| Aspect | Old (0.16.0) | New (current) |
|---|---|---|
| **Input parsing** | nikic/php-parser → AST nodes | PhpToken::tokenize() → token stream |
| **Core model** | 89 Printable classes (AST constructs) | 507 AToken classes (lexical tokens) |
| **Transformation** | Visitor pattern on AST; Printer flattening | Token `parse()` callbacks; synthetic token injection |
| **Styling** | Imperative `s*()` method overrides on Styler | Declarative `styles` array on Format |
| **Splitting** | Integrated in Styler | Separate Splitter stage with priority-ordered TSplit tokens |
| **Rules** | None (all logic in Styler methods) | Composable TokenRule and LineRule implementations |
| **Customization** | Subclass Styler, override methods | Configure Format with styles/rules/parseAs arrays, or extend Format |
| **Pipeline** | Parse → Visit → Print → Style → Render | Parse → TokenRules → Assemble → Split → LineRules → Render |
| **Dependencies** | nikic/php-parser ^4.19 | None (PHP built-in only) |
| **PHP version** | 8.1+ | 8.4+ |

### Why the Rewrite

The AST-based approach had fundamental limitations:

1. **Comment loss.** nikic/php-parser's AST representation discards positional
   information for comments, causing them to be lost or misplaced.
2. **String mangling.** AST reconstruction of interpolated strings converted
   literal whitespace to escape sequences.
3. **Monolithic customization.** Every formatting decision was an imperative
   method override, making changes fragile and hard to compose.
4. **Heavy dependency.** nikic/php-parser is a large, complex library for a task
   that only needs lexical token manipulation.

The token-based approach works at a lower level of abstraction, preserving the
original source tokens and their positions. This trades AST-level semantic
understanding for faithful token preservation and simpler, more composable
formatting logic.
