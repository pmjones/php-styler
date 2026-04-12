<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_NULLSAFE_OBJECT_OPERATOR
 *
 * Syntax: ?->
 *
 * Reference: https://www.php.net/manual/en/language.oop5.php classes and objects
 */
class TNullsafeObjectOperator extends AToken implements ASplittableFluent
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $class = $parser->inEncapsedString()
            ? TEncapsedNullsafeObjectOperator::class
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
