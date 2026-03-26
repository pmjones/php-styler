<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TConstComma extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        // determine semicolon class BEFORE popping nesting
        $semicolonClass = $parser->atNesting(
            TConst::class,
            TClasslikeOpeningBrace::class,
        )
            ? TConstEndSemicolon::class
            : TNamespaceConstEndSemicolon::class;

        // close current const declaration
        $closer = new PhpToken(ord(';'), ';');
        $parser->parse($closer, $semicolonClass);

        // scan backward in source to find T_CONST and modifiers before it
        $sourceOffset = $parser->getSourceOffset();
        $constOffset = null;

        for ($i = $sourceOffset - 1; $i >= 0; $i --) {
            if ($parser->getSourceAt($i)->is(T_CONST)) {
                $constOffset = $i;
                break;
            }
        }

        $modifiers = [];

        if ($constOffset !== null) {
            for ($i = $constOffset - 1; $i >= 0; $i --) {
                $token = $parser->getSourceAt($i);

                if ($token->is(T_WHITESPACE)) {
                    continue;
                }

                if (
                    $token->is(
                        [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_FINAL, T_READONLY],
                    )
                ) {
                    $modifiers[] = $token;
                    continue;
                }

                break;
            }
        }

        // build replacement source tokens
        $replacement = [];
        $replacement[] = new PhpToken(T_WHITESPACE, "\n");

        foreach (array_reverse($modifiers) as $mod) {
            $replacement[] = new PhpToken($mod->id, $mod->text);
            $replacement[] = new PhpToken(T_WHITESPACE, ' ');
        }

        $replacement[] = new PhpToken(T_CONST, 'const');

        // splice into source after the comma — parser continues naturally
        $parser->spliceSource($sourceOffset + 1, 0, $replacement);
    }
}
