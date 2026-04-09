<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_FUNCTION
 *
 * Syntax: function
 *
 * Reference: https://www.php.net/manual/en/language.functions.php functions
 */
class TFunction extends AToken
{
    public const OPENING_BRACE = TFunctionOpeningBrace::class;

    public const CLOSING_BRACE = TFunctionClosingBrace::class;

    public const END_SEMICOLON = TAbstractMethodEndSemicolon::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TUse::class)) {
            $parser->popNesting(TUse::class);
            $parser->addNesting($source, TUseFunction::class);
            return;
        }

        if ($parser->getNextSource()?->is('(')) {
            $parser->parse($source, TAnonymousFunction::class);
            return;
        }

        $parser->reclassifyNextSourceAsName();

        $nestingClass = self::class;

        if ($parser->atClassBody()) {
            $next = $parser->getNextSource();
            $nameText = null;

            if ($next?->is(T_STRING)) {
                $nameText = $next->text;
            } elseif ($next?->is(T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG)) {
                $nameToken = $parser->getNextSource(skip: 1);

                if ($nameToken?->is(T_STRING)) {
                    $nameText = $nameToken->text;
                }
            }

            if (
                $nameText !== null
                && in_array($nameText, TMagicMethodName::NAMES, true)
            ) {
                $nestingClass = TMagicMethod::class;
            }
        }

        $parser->addNesting($source, $nestingClass);
    }
}
