<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_NEW
 *
 * Syntax: new
 *
 * Reference: https://www.php.net/manual/en/language.oop5.php classes and objects
 */
class TNew extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $prev = $parser->getPrevParsed();

        if ($prev instanceof TFunction || $prev instanceof TReference) {
            $parser->add($source, TFunctionName::class);
            return;
        }

        parent::parse($parser, $source);

        // look ahead to inject () for bare instantiation
        $nameOffset = $parser->findNextNonWhitespaceOffset();

        if ($nameOffset === null) {
            return;
        }

        $nameToken = $parser->getSourceAt($nameOffset);

        if (
            ! $nameToken->is([
                T_STRING,
                T_NAME_QUALIFIED,
                T_NAME_FULLY_QUALIFIED,
                T_VARIABLE,
            ])
        ) {
            return;
        }

        $afterOffset = $parser->findNextNonWhitespaceOffset($nameOffset + 1);

        if ($afterOffset !== null && $parser->getSourceAt($afterOffset)->is('(')) {
            return;
        }

        // for variables, don't inject if expression continues
        if (
            $nameToken->is(T_VARIABLE)
            && $afterOffset !== null
            && $parser
                ->getSourceAt($afterOffset)
                ->is([
                    T_OBJECT_OPERATOR,
                    T_NULLSAFE_OBJECT_OPERATOR,
                    T_DOUBLE_COLON,
                    '[',
                ])
        ) {
            return;
        }

        $parser->spliceSource(
            $nameOffset + 1,
            0,
            [new PhpToken(ord('('), '('), new PhpToken(ord(')'), ')')],
        );
    }
}
