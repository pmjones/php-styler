<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_NEW
 *
 * Syntax: new
 *
 * Reference: https://www.php.net/manual/en/language.oop5.php classes and objects
 */
class TNew extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $prev = $parser->getPrevParsed();

        if ($prev instanceof TFunction || $prev instanceof TReference) {
            $parser->add($unparsed, TFunctionName::class);
            return;
        }

        parent::parse($parser, $unparsed);
    }
}
