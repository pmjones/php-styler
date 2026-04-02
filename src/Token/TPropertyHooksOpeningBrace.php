<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPropertyHooksOpeningBrace extends AToken implements TOpeningStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if (static::hooksAreAbstract($parser)) {
            $parser->parse($source, TPropertyHooksAbstractOpeningBrace::class);
            return;
        }

        $parser->addNesting($source, static::class);
        $parser->indentIncr();
    }

    protected static function hooksAreAbstract(Parser $parser) : bool
    {
        if ($parser->atNesting(TInterfaceOpeningBrace::class)) {
            return true;
        }

        for ($i = $parser->getParsedCount() - 1; $i >= 0; $i --) {
            $prev = $parser->getParsedAt($i);

            if ($prev instanceof TOpeningStructure) {
                return false;
            }

            if ($prev instanceof TAbstract) {
                return true;
            }
        }

        return false;
    }
}
