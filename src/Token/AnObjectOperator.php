<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

abstract class AnObjectOperator extends AToken implements ASplittableFluent
{
    /** @var class-string<AToken> */
    protected const ENCAPSED_CLASS = AToken::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $class = $parser->inEncapsedString()
            ? static::ENCAPSED_CLASS
            : static::class;

        $parser->add($source, $class);
        $parser->reclassifyNextSourceAsName();
    }

    public function splitBefore(Parser $parser) : ?TSplit
    {
        $split = new TSplitPropertyAccess(AToken::SYNTHETIC, '');

        $newChain = $parser->getPrevParsed() instanceof TVariable
            && ! $parser->isPrevStaticPropertyAccess();

        [$split->chainIndex, $split->chainPosition] = $newChain
            ? $parser->startFluentChain()
            : $parser->continueFluentChain();

        return $split;
    }
}
