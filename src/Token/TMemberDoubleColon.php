<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
class TMemberDoubleColon extends T implements TSplittableFluent
{
    public function splitBefore(Parser $parser) : ?TSplit
    {
        return $parser->getNextSource()?->is(T_VARIABLE)
            ? new TSplitStaticMember(T::SYNTHETIC, '')
            : new TSplitStaticMethodCall(T::SYNTHETIC, '');
    }
}
