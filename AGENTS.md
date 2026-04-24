# AGENTS.md

## Project Overview

PHP code formatter that parses source into custom token objects, applies transformation rules, assembles tokens into lines, splits long lines, and renders the result. Uses PHP's native `PhpToken::tokenize()` internally.

## Key Commands

- `composer check` — tests, then PHPStan, then style check, in order (fails fast). **Run this to verify all changes.**
- `composer test` — PHPUnit tests only
- `composer analyze` — PHPStan static analysis only
- `composer cs-check` — check code style using this project's own formatter
- `composer cs-fix` — apply code style fixes
- `composer cs-preview` — show styled output without modifying files
- `composer test-coverage` — generate HTML coverage report to `tmp/coverage/`

## Gotchas

- Do not run `composer cs-fix` on in-flight code; fix style issues manually.
- New test files at the `tests/` top level must be added to `php-styler.php` or `composer cs-check` won't cover them.
- `src/Token/` has ~550 files — use grep/search, not directory listing.

## Architecture

### Pipeline

`Styler::__invoke(string $code): string` runs 6 stages:

1. **Parse** (`Parser`) — tokenizes PHP into `AToken` objects, tracking nesting and paren depth. The Parser also handles modifier ordering and missing-visibility insertion directly during tokenization (via `handleModifier()`, `atClassBody()`, `hasPrevVisibility()`), and always adds control structure braces. Token classes can detect abstract property hooks via nesting (`atNesting()`) and parsed-token inspection (`hasPrev()`, `getParsedAt()`), and reorder hook tokens at the closing brace (`swapParsedAt()`).
2. **TokenRules** — `TokenRule` implementations transform the token array (e.g., `NormalizeImports`, `NormalizeTypeOrder`)
3. **Assemble** (`Assembler`) — groups tokens into `Line` objects by indent level
4. **Split** (`Splitter`) — enforces line-length limits via prioritized split points; runs `normalizeIndents` twice (before and after expansion)
5. **LineRules** — `LineRule` implementations transform lines (e.g., `MergeParenBrace`, `NormalizeTrailingCommas`)
6. **Render** — each `Line` renders its tokens to string with indentation

### Key Directories

- `src/Token/` — `AToken` subclasses in a flat directory (no subdirs, ~550 files: 519 concrete `T*` tokens, 30 `A*` interfaces/abstracts, and the `DocblockParsing` trait used by the 16 comment-token classes to share the `getDocblock()` method). Includes whitespace tokens, split-point markers (`TSplit` subclasses with priority), and marker interfaces for classification. Many of the simpler token classes inherit `AToken::parse()` without overriding — a dedicated `parse()` is only needed when the token has contextual dispatch logic beyond "add self and optional space-after."
- `src/Rule/` — 27 concrete rule implementations. TokenRules (22): `CollapseEmptyBody`, `ConvertFromYodaConditions`, `ConvertToShortArraySyntax`, `ConvertToShortListSyntax`, `ConvertToYodaConditions`, `ConvertVarToPublic`, `ExpandConstDeclarations`, `ExpandGroupedImports`, `ExpandPropertyDeclarations`, `InsertNewParens`, `InsertPublicVisibility`, `MergeParenBracket`, `NormalizeImports`, `NormalizeModifierOrder`, `NormalizeTypeOrder`, `RemoveBom`, `RemoveEmptyAnonymousClassParens`, `RemoveEmptyAttributeParens`, `RemoveLanguageConstructParens`, `RemoveLeadingWhitespace`, `RemovePhpClosingTag`, `RemoveRepeatedSemicolons`. LineRules (5): `MergeParenBrace`, `NormalizeMemberOrder`, `NormalizeMemberSpacing`, `NormalizeTrailingCommas`, `RemoveTrailingBlankLines`. Configured in Format classes as `[RuleClass::class => [args]]`.
- `src/Format/` — `AFormat` interface → `PlainFormat` (base with styles) → `DeclarationFormat` (opinionated defaults: next-line braces, lower keywords, default rules). Vendor formats in `src/Format/Vendor/` (`DoctrineFormat`, `Percs30Format`, `SymfonyFormat`).
- `src/Parallel/` — `WorkerPool` (spawns child processes via `proc_open` for parallel file processing) and `WorkerResult` (per-file result value object).
- `src/Command/` — CLI commands (`Apply`, `Check`, `Diff`, `Preview`) with corresponding `*Options` classes, plus an internal `Worker` command for parallel execution. Dispatched via `AutoShell\Console` from `bin/php-styler`. The `apply`, `check`, and `diff` commands accept `--workers=N` (or `auto`) to process files in parallel.

### Configuration

`php-styler.php` at project root returns a `Config` object specifying `files` and `format`. The `files` list explicitly enumerates which paths are style-checked — `src/`, `tests/Rule/`, `tests/Token/`, and individual test files. `tests/Examples/` and `tests/Format/` are not style-checked.

## Coding Conventions

- PHP 8.4+ required; uses PHP 8.4 features (asymmetric visibility, interface property hooks)
- PSR-4 autoloading: `PhpStyler\` → `src/`
- 4-space indentation, LF line endings
- PHPStan level max (covers `src/` and `tests/`, excludes `tests/Examples/`)
- The project formats its own source code with its own tool
- CI runs on Linux and Windows, PHP 8.4 only (`.github/workflows/ci.yml`)

## Testing

- PHPUnit 11, config in `phpunit.xml`, bootstrap in `phpunit.php`
- Test namespace: `PhpStyler\` maps to `tests/` via autoload-dev
- Line coverage is at 100% and should stay there — the campaign behind the surviving `@codeCoverageIgnore` markers preferred real tests over annotations where possible; see `tests/Token/T*DefensiveTest.php`, `tests/ParserInvariantsTest.php`, and `tests/ParserResilienceTest.php` for the synthetic-state and invalid-input patterns used to exercise defensive branches.
- `tests/TestCase.php` — base class with a `$styler` (DeclarationFormat, rules limited to `RemoveTrailingBlankLines`) and `assertPrint()` helper
- `tests/ExamplesTest.php` — data-driven from `tests/Examples/*.php`; verifies styling is idempotent. Uses `TestFormat` with only 4 rules (`MergeParenBracket`, `MergeParenBrace`, `NormalizeTrailingCommas`, `RemoveTrailingBlankLines`), not `DeclarationFormat`'s full rule set.
- `tests/Token/` — data-driven token parsing tests extending `TTestCase`; plus `T*DefensiveTest.php` standalone tests that manufacture pathological parser state to exercise invariant-violation paths.
- `tests/Rule/` — rule-specific tests with custom format; several include "resilience" tests that feed hand-built malformed token streams to verify rules don't crash on corrupt input from upstream rules.
- `tests/Format/` — format-level tests, including vendor format tests in `tests/Format/Vendor/` (Doctrine, Percs30, Symfony).
- `tests/ParserInvariantsTest.php` and `tests/ParserResilienceTest.php` — parser safety nets: the first synthesizes pathological stack states; the second feeds invalid PHP that PHP itself rejects, verifying the styler fails loud with a diagnosable message rather than producing silently-wrong output.

## Working in This Codebase

- Always run `composer check` before considering a change complete.
- To debug pipeline issues, start at `Styler::__invoke()` in `src/Styler.php` and trace into the relevant stage.
- When adding a new rule, create the class in `src/Rule/` implementing `TokenRule` or `LineRule`, add it to `DeclarationFormat::$rules`, and create a test in `tests/Rule/`.
- Use `// @php-styler-expansive` in comments to force array/line expansion (used heavily in test data providers).

## Branching

- Development on `0.x`
- PRs target `0.x`
