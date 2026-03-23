<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TDynamicMemberClosingBrace extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->closeNesting(
            $source,
            self::class,
            TDynamicMemberOpeningBrace::class,
        );

        if ($parser->getNextSource()?->is('(') && $parser->lastSplit !== null) {
            if ($parser->lastSplit instanceof TSplitStaticMember) {
                $parser->replaceLastSplit(
                    new TSplitStaticMethodCall(AToken::SYNTHETIC, ''),
                );
            } else {
                $parser->replaceLastSplit(
                    new TSplitMethodCall(AToken::SYNTHETIC, ''),
                );
            }
        }
    }
}
