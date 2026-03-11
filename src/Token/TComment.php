<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_COMMENT
 *
 * Syntax: //, /*, #
 *
 * Reference: https://www.php.net/manual/en/language.basic-syntax.comments.php comments
 *
 */
class TComment extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parseClass = match (substr($source->text, 0, 2)) {
            '//' => TCommentSlashed::class,
            '/*' => TCommentStarred::class,
            default => TCommentHashed::class,
        };

        $parser->parse($source, $parseClass);
    }
}
