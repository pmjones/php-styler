<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
use PhpStyler\Token\TArrayClosingBracket;
use PhpStyler\Token\TArrayConstruct;
use PhpStyler\Token\TArrayConstructClosingParen;
use PhpStyler\Token\TArrayConstructOpeningParen;
use PhpStyler\Token\TArrayOpeningBracket;
use PhpStyler\Token\TSpace;

class ConvertLongArrayToShort implements TokenRule
{
    /**
     * @param T[] $tokens
     * @return T[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);
        $pendingClosers = [];

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if ($token instanceof TArrayConstruct) {
                if (isset($tokens[$i + 1]) && $tokens[$i + 1] instanceof TSpace) {
                    $i ++;
                }

                continue;
            }

            if ($token instanceof TArrayConstructOpeningParen) {
                $newOpener = new TArrayOpeningBracket(
                    ord('['),
                    '[',
                    $token->line,
                    $token->pos,
                );
                $newOpener->argCount = $token->argCount;
                $newOpener->parenDepth = $token->parenDepth;
                if ($token->closingToken === null) {
                    $result[] = $token;
                    continue;
                }

                $pendingClosers[spl_object_id($token->closingToken)] = $newOpener;
                $result[] = $newOpener;
                continue;
            }

            if ($token instanceof TArrayConstructClosingParen) {
                $newCloser = new TArrayClosingBracket(
                    ord(']'),
                    ']',
                    $token->line,
                    $token->pos,
                );
                $newCloser->parenDepth = $token->parenDepth;
                $oid = spl_object_id($token);

                if (isset($pendingClosers[$oid])) {
                    $newOpener = $pendingClosers[$oid];
                    $newOpener->closingToken = $newCloser;
                    $newCloser->openingToken = $newOpener;
                    unset($pendingClosers[$oid]);
                }

                $result[] = $newCloser;
                continue;
            }

            $result[] = $token;
        }

        return $result;
    }
}
