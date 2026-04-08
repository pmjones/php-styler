<?php
declare(strict_types=1);

namespace PhpStyler\Format;

use PhpStyler\Rule\LineRule\ALineRule;
use PhpStyler\Rule\TokenRule\ATokenRule;
use PhpStyler\Token\AToken;

/**
 * @phpstan-type parse_as_array array<token_class_string, token_class_string>
 *
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
 * @phpstan-type rule_class_string class-string<ATokenRule|ALineRule>
 *
 * @phpstan-type rule_args array<string, mixed>
 *
 * @phpstan-type rules_array array<rule_class_string, rule_args>
 */
abstract class AFormat
{
    public string $eol = "\n";

    public int $lineLen = 84;

    public int $indentLen = 4;

    public bool $indentTab = false;

    /** @var parse_as_array */
    public array $parseAs = [];

    /** @var styles_array */
    public array $styles = [];

    /** @var rules_array */
    public array $rules = [];
}
