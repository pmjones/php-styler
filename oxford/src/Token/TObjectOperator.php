<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_OBJECT_OPERATOR
 *
 * Syntax: ->
 *
 * Reference: https://www.php.net/manual/en/language.oop5.php classes and objects
 */
class TObjectOperator extends T implements TSplittableFluent
{
    public function splitCategory() : int
    {
        return self::FLUENT;
    }

    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $class = $parser->atNesting(TCurlyOpen::class)
            || $parser->atNesting(TDollarOpenCurlyBraces::class)
            ? TEncapsedObjectOperator::class
            : static::class;
        $parser->add($unparsed, $class);
    }
}
