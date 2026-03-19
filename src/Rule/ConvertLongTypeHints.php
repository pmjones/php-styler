<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
use PhpStyler\Token\TBool;
use PhpStyler\Token\TBoolean;
use PhpStyler\Token\TDouble;
use PhpStyler\Token\TFloat;
use PhpStyler\Token\TInt;
use PhpStyler\Token\TInteger;
use PhpStyler\Token\TReal;

class ConvertLongTypeHints implements TokenRule
{
    private const REPLACEMENTS = [
        TInteger::class => [TInt::class, 'int'],
        TBoolean::class => [TBool::class, 'bool'],
        TDouble::class => [TFloat::class, 'float'],
        TReal::class => [TFloat::class, 'float'],
    ];

    /**
     * @param T[] $tokens
     * @return T[]
     */
    public function apply(array $tokens) : array
    {
        foreach ($tokens as $i => $token) {
            $class = get_class($token);

            if (isset(self::REPLACEMENTS[$class])) {
                [$newClass, $newText] = self::REPLACEMENTS[$class];
                $tokens[$i] = new $newClass($token->id, $newText, $token->line, $token->pos);
            }
        }

        return $tokens;
    }
}
