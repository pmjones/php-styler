<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TFunctionOpeningBrace extends T implements TOpeningStructure
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $rejoin = $parser->getPrevParsed() instanceof TParamsClosingParen;
        $token = $parser->addNesting($unparsed, static::class);
        $parser->indentIncr();

        if ($rejoin) {
            $token->rejoinOrphanBefore = true;
        }
    }
}
