<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
use PhpStyler\Token\TArgsClosingParen;
use PhpStyler\Token\TArgsComma;
use PhpStyler\Token\TArgsOpeningParen;
use PhpStyler\Token\TArrayClosingBracket;
use PhpStyler\Token\TArrayComma;
use PhpStyler\Token\TArrayOpeningBracket;
use PhpStyler\Token\TList;
use PhpStyler\Token\TSpace;

class ConvertListToArray implements TokenRule
{
    /**
     * @param T[] $tokens
     * @return T[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);
        $expectOpener = false;
        $pendingClosers = [];
        $activeClosers = [];

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if ($token instanceof TList) {
                if (isset($tokens[$i + 1]) && $tokens[$i + 1] instanceof TSpace) {
                    $i ++;
                }

                $expectOpener = true;
                continue;
            }

            if ($expectOpener && $token instanceof TArgsOpeningParen) {
                $expectOpener = false;
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

                $closerOid = spl_object_id($token->closingToken);
                $pendingClosers[$closerOid] = $newOpener;
                $activeClosers[$closerOid] = true;
                $result[] = $newOpener;
                continue;
            }

            $expectOpener = false;

            if ($token instanceof TArgsClosingParen) {
                $oid = spl_object_id($token);

                if (isset($activeClosers[$oid])) {
                    $newCloser = new TArrayClosingBracket(
                        ord(']'),
                        ']',
                        $token->line,
                        $token->pos,
                    );
                    $newCloser->parenDepth = $token->parenDepth;

                    if (isset($pendingClosers[$oid])) {
                        $newOpener = $pendingClosers[$oid];
                        $newOpener->closingToken = $newCloser;
                        $newCloser->openingToken = $newOpener;
                        unset($pendingClosers[$oid]);
                    }

                    unset($activeClosers[$oid]);
                    $result[] = $newCloser;
                    continue;
                }
            }

            if ($token instanceof TArgsComma && ! empty($activeClosers)) {
                $result[] = new TArrayComma(ord(','), ',', $token->line, $token->pos);
                continue;
            }

            $result[] = $token;
        }

        return $result;
    }
}
