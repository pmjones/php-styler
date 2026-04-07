<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ECHO
 *
 * Syntax: echo
 *
 * Reference: https://www.php.net/manual/en/function.echo.php echo
 */
class TEcho extends ALanguageConstruct
{
    public const END_SEMICOLON = TEchoEndSemicolon::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
