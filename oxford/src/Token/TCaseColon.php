<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TCaseColon extends T implements TOpeningStructure
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if ($parser->getNextUnparsed()?->is([T_CASE, T_DEFAULT])) {
            $parser->popNesting(TCase::class, TDefaultCase::class, TCaseAfterCase::class, TDefaultAfterCase::class);

            $parser->add($unparsed, TCaseFallthroughColon::class);
            return;
        }

        $parser->addNesting($unparsed, self::class);

        $parser->indentIncr();
    }
}
