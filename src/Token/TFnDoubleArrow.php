<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TFnDoubleArrow extends AToken implements ASplittableOperator, AFnNesting
{
    public const END_SEMICOLON = TFnEndSemicolon::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }

    public function splitBefore(Parser $parser) : ?TSplit
    {
        return new TSplitFnDoubleArrow(AToken::SYNTHETIC, '');
    }
}
