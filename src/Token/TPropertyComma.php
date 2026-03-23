<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPropertyComma extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        // close current property declaration
        $closer = new PhpToken(ord(';'), ';');
        $parser->parse($closer, TPropertyEndSemicolon::class);

        // scan backward in source to find T_VARIABLE
        $sourceOffset = $parser->getSourceOffset();
        $variableOffset = null;

        for ($i = $sourceOffset - 1; $i >= 0; $i --) {
            if ($parser->getSourceAt($i)->is(T_VARIABLE)) {
                $variableOffset = $i;
                break;
            }
        }

        // collect prefix tokens (modifiers + type) before T_VARIABLE
        $prefix = [];

        if ($variableOffset !== null) {
            for ($i = $variableOffset - 1; $i >= 0; $i --) {
                $token = $parser->getSourceAt($i);

                if ($token->is(T_WHITESPACE)) {
                    continue;
                }

                if (
                    $token->is(['{', '}', ';', ',', ']'])
                    || $token->is(
                        [T_OPEN_TAG, T_DOC_COMMENT, T_COMMENT, T_CLOSE_TAG],
                    )
                ) {
                    break;
                }

                $prefix[] = $token;
            }
        }

        // build replacement source tokens
        $replacement = [];
        $replacement[] = new PhpToken(T_WHITESPACE, "\n");

        foreach (array_reverse($prefix) as $tok) {
            $replacement[] = new PhpToken($tok->id, $tok->text);
            $replacement[] = new PhpToken(T_WHITESPACE, ' ');
        }

        // splice into source after the comma
        $parser->spliceSource($sourceOffset + 1, 0, $replacement);
    }
}
