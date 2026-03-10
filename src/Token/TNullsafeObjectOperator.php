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
class TNullsafeObjectOperator extends T implements TSplittableFluent
{
    public function splitCategory() : int
    {
        return self::FLUENT;
    }

    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $class = $parser->atNesting(TCurlyOpen::class)
            || $parser->atNesting(TDollarOpenCurlyBraces::class)
            ? TEncapsedNullsafeObjectOperator::class
            : static::class;
        $parser->add($unparsed, $class);
    }
}
