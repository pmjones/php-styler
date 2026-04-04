<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;

class TMemberDoubleColon extends AToken implements ASplittableFluent
{
    public function splitBefore(Parser $parser) : ?TSplit
    {
        $next = $parser->getNextSource();

        if ($next?->is(T_VARIABLE) || $next?->is('{')) {
            return new TSplitStaticMember(AToken::SYNTHETIC, '');
        }

        // Only split for static method calls (member name followed by '('),
        // not for constants, enum cases, or ::class
        $memberOffset = $parser->findNextNonWhitespaceOffset();

        if ($memberOffset === null) {
            return null;
        }

        $afterMemberOffset = $parser->findNextNonWhitespaceOffset(
            $memberOffset + 1,
        );

        if (
            $afterMemberOffset !== null
            && $parser->getSourceAt($afterMemberOffset)->is('(')
        ) {
            return new TSplitStaticMethodCall(AToken::SYNTHETIC, '');
        }

        return null;
    }
}
