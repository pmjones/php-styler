<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;

class TMemberDoubleColon extends AToken implements TSplittableFluent
{
    public function splitBefore(Parser $parser) : ?TSplit
    {
        $next = $parser->getNextSource();

        return $next?->is(T_VARIABLE)
            || $next?->is('{')
            ? new TSplitStaticMember(AToken::SYNTHETIC, '')
            : new TSplitStaticMethodCall(AToken::SYNTHETIC, '');
    }
}
