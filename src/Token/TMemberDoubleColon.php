<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;

class TMemberDoubleColon extends AToken implements ASplittableFluent
{
    public function splitBefore(Parser $parser) : ?TSplit
    {
        $next = $parser->source->peek();

        if ($next?->is(T_VARIABLE) || $next?->is('{')) {
            $split = new TSplitStaticMember(AToken::SYNTHETIC, '');

            [
                $split->chainIndex,
                $split->chainPosition,
            ] = $parser->startFluentChain();

            return $split;
        }

        // Only split for static method calls (member name followed by '('),
        // not for constants, enum cases, or ::class
        $memberOffset = $parser->source->findNextNonWhitespace();

        // @codeCoverageIgnoreStart
        // defensive: valid PHP always has content after `::`
        if ($memberOffset === null) {
            return null;
        }

        // @codeCoverageIgnoreEnd

        $afterMemberOffset = $parser->source
            ->findNextNonWhitespace($memberOffset + 1);

        if (
            $afterMemberOffset !== null
            && $parser->source->getAt($afterMemberOffset)->is('(')
        ) {
            $split = new TSplitStaticMethodCall(AToken::SYNTHETIC, '');

            [
                $split->chainIndex,
                $split->chainPosition,
            ] = $parser->startFluentChain();

            return $split;
        }

        return null;
    }
}
