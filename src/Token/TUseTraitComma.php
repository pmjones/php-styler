<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TUseTraitComma extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        // conflict resolution (use A, B { ... }) — keep comma
        $sourceCount = $parser->getSourceCount();
        $offset = $parser->getSourceOffset();

        for ($i = $offset + 1; $i < $sourceCount; $i ++) {
            $token = $parser->getSourceAt($i);

            if ($token->is(';')) {
                break; // simple trait use — expand
            }

            if ($token->is('{')) {
                $parser->add($source, static::class);
                return;
            }
        }

        // expand: close current trait use, open new one
        $closer = new PhpToken(ord(';'), ';');
        $parser->parse($closer, TUseTraitEndSemicolon::class);

        $opener = new PhpToken(T_USE, 'use');
        $parser->addNesting($opener, TUseTrait::class);
    }
}
