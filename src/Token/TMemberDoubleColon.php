<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TMemberDoubleColon extends T implements TSplittableFluent
{
    public function splitBefore(Parser $parser) : ?TSplit
    {
        return $parser->getNextSource()?->is(T_VARIABLE)
            ? new TSplitStaticMember(T::SYNTHETIC, '')
            : new TSplitStaticMethodCall(T::SYNTHETIC, '');
    }

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->noSpace();
        $parser->add($source, static::class);
    }
}
