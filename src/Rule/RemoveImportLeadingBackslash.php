<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TFullyQualifiedName;
use PhpStyler\Token\TQualifiedName;
use PhpStyler\Token\TUse;
use PhpStyler\Token\TUseConst;
use PhpStyler\Token\TUseEndSemicolon;
use PhpStyler\Token\TUseFunction;

class RemoveImportLeadingBackslash implements TokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $inImport = false;
        $count = count($tokens);

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if (
                $token instanceof TUse
                || $token instanceof TUseFunction
                || $token instanceof TUseConst
            ) {
                $inImport = true;
                continue;
            }

            if ($token instanceof TUseEndSemicolon) {
                $inImport = false;
                continue;
            }

            if ($inImport && $token instanceof TFullyQualifiedName) {
                $tokens[$i] = new TQualifiedName(
                    $token->id,
                    ltrim($token->text, '\\'),
                    $token->line,
                    $token->pos,
                );
            }
        }

        return $tokens;
    }
}
