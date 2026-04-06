<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_OBJECT_OPERATOR
 *
 * Syntax: ->
 *
 * Reference: https://www.php.net/manual/en/language.oop5.php classes and objects
 */
class TObjectOperator extends AToken implements ASplittableFluent
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $class = $parser->inEncapsedString()
            ? TEncapsedObjectOperator::class
            : static::class;

        $parser->add($source, $class);
    }

    public function splitBefore(Parser $parser) : ?TSplit
    {
        return new TSplitPropertyAccess(AToken::SYNTHETIC, '');
    }
}
