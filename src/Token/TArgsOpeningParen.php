<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TArgsOpeningParen extends ACommaListOpener implements AnArgsOpener
{
    public const EXPAND_PRIORITY = ASplittable::OTHER_PAREN;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
        $parser->source->reclassifyNextNamedArg();
    }

    public function commaClass() : string
    {
        return TArgsComma::class;
    }
}
