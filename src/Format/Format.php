<?php
declare(strict_types=1);

namespace PhpStyler\Format;

use PhpStyler\Rule\LineRule;
use PhpStyler\Rule\TokenRule;
use PhpStyler\Token\AToken;

/**
 * @phpstan-type token_class_string class-string<AToken>
 *
 * @phpstan-type styles_array array<token_class_string, style_args_array>
 *
 * @phpstan-type style_args_array array{
 *     spaceBefore?: ?bool,
 *     spaceAfter?: ?bool,
 *     lineBreakBefore?: ?bool,
 *     lineBreakAfter?: ?bool,
 *     blankLineBefore?: ?bool,
 *     blankLineAfter?: ?bool,
 *     case?: ?(callable(string): string),
 * }
 *
 * @phpstan-type rule_class_string class-string<TokenRule|LineRule>
 *
 * @phpstan-type rule_args array<string, mixed>
 *
 * @phpstan-type rules_array array<rule_class_string, rule_args>
 */
interface Format
{
    public string $eol {
        get;
    }
    public int $lineLen {
        get;
    }
    public int $indentLen {
        get;
    }
    public bool $indentTab {
        get;
    }
    /** @var styles_array */
    public array $styles {
        get;
    }
    /** @var rules_array */
    public array $rules {
        get;
    }
}
