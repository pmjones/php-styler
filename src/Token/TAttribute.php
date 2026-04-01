<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ATTRIBUTE
 *
 * Syntax: #[
 *
 * Reference: https://www.php.net/manual/en/language.attributes.php attributes (available as of PHP 8.0.0)
 */
class TAttribute extends AToken implements TAttribution
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TParamsOpeningParen::class)) {
            $parser->parse($source, TInlineAttribute::class);
        } else {
            $parser->addNesting($source, self::class);
        }

        // remove empty () from attribute declarations
        $offset = $parser->findNextNonWhitespaceOffset();

        while ($offset !== null) {
            if ($parser->getSourceAt($offset)->is(']')) {
                break;
            }

            if ($parser->getSourceAt($offset)->is('(')) {
                $closeOffset = $parser->findNextNonWhitespaceOffset($offset + 1);

                if (
                    $closeOffset !== null
                    && $parser->getSourceAt($closeOffset)->is(')')
                ) {
                    $parser->spliceSource($offset, $closeOffset - $offset + 1, []);
                    $offset = $parser->findNextNonWhitespaceOffset($offset);
                    continue;
                }

                // non-empty parens — skip to matching close paren
                $matchingClose = $parser->findMatchingCloseParenOffset($offset);

                if ($matchingClose !== null) {
                    $offset = $parser->findNextNonWhitespaceOffset(
                        $matchingClose + 1,
                    );

                    continue;
                }

                break;
            }

            $offset = $parser->findNextNonWhitespaceOffset($offset + 1);
        }
    }
}
