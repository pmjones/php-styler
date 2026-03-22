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
class TPrint extends ALanguageConstruct
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $prev = $parser->getPrevParsed();

        if ($prev instanceof TFunction || $prev instanceof TReference) {
            $parser->add($source, TFunctionName::class);
            return;
        }

        self::tryRemoveParens($parser);
        parent::parse($parser, $source);
    }
}
