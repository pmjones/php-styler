<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TCaseColon extends T implements TOpeningStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->getNextSource()?->is([T_CASE, T_DEFAULT])) {
            $parser->popNesting(
                TCase::class,
                TDefaultCase::class,
                TCaseAfterCase::class,
                TDefaultAfterCase::class,
            );

            $parser->add($source, TCaseFallthroughColon::class);
            return;
        }

        $parser->addNesting($source, self::class);

        $parser->indentIncr();
    }
}
