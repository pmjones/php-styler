<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPropertyHooksAbstractClosingBrace extends AToken implements
    AClosingStructure,
    AMemberClosing
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $setIndex = null;
        $getIndex = null;

        for ($i = $parser->getParsedCount() - 1; $i >= 0; $i --) {
            $token = $parser->getParsedAt($i);

            if ($token instanceof TPropertyHooksAbstractOpeningBrace) {
                break;
            }

            if ($token instanceof TPropertyHookSetAbstract) {
                $setIndex = $i;
            }

            if ($token instanceof TPropertyHookGetAbstract) {
                $getIndex = $i;
            }
        }

        if ($setIndex !== null && $getIndex !== null && $setIndex < $getIndex) {
            for ($offset = 0; $offset < 3; $offset ++) {
                $parser->swapParsedAt($setIndex + $offset, $getIndex + $offset);
            }
        }

        $parser->closeNesting(
            $source,
            self::class,
            TPropertyHooksAbstractOpeningBrace::class,
        );
    }

    public bool $closesStaticMember = false;

    public function memberType() : string
    {
        return AMemberClosing::PROPERTY;
    }
}
