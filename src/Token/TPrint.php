<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_PRINT
 *
 * Syntax: print
 *
 * Reference: https://www.php.net/manual/en/function.print.php print
 */
class TPrint extends T
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
