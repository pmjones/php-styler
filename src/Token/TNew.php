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
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $prev = $parser->getPrevParsed();

        if ($prev instanceof TFunction || $prev instanceof TReference) {
            $parser->add($source, TFunctionName::class);
            return;
        }

        parent::parse($parser, $source);
    }
}
