<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Docblock;
use PhpStyler\Parser;
use PhpToken;

class TDocCommentLineBreak extends T implements TCommentary, TDocblock
{
    protected ?Docblock $docblock = null;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->space();
        $parser->add($source, static::class);
        $parser->lineBreak();
    }

    public function getDocblock() : Docblock
    {
        return $this->docblock ??= Docblock::parse($this->text);
    }
}
