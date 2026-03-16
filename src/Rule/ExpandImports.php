<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TNamespaceSeparator;
use PhpStyler\Token\TQualifiedName;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TSplit;
use PhpStyler\Token\TUnqualifiedName;
use PhpStyler\Token\TUse;
use PhpStyler\Token\TUseAlias;
use PhpStyler\Token\TUseAs;
use PhpStyler\Token\TUseClosingBrace;
use PhpStyler\Token\TUseComma;
use PhpStyler\Token\TUseConst;
use PhpStyler\Token\TUseConstClosingBrace;
use PhpStyler\Token\TUseConstOpeningBrace;
use PhpStyler\Token\TUseEndSemicolon;
use PhpStyler\Token\TUseFunction;
use PhpStyler\Token\TUseFunctionClosingBrace;
use PhpStyler\Token\TUseFunctionOpeningBrace;
use PhpStyler\Token\TUseOpeningBrace;

class ExpandImports implements TokenRule
{
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);
        $i = 0;

        while ($i < $count) {
            $token = $tokens[$i];

            if (
                ! $token instanceof TUseOpeningBrace
                && ! $token instanceof TUseFunctionOpeningBrace
                && ! $token instanceof TUseConstOpeningBrace
            ) {
                $result[] = $token;
                $i ++;
                continue;
            }

            $openingBrace = $token;
            $openingBracePos = $i;

            // determine closing brace class
            if ($openingBrace instanceof TUseFunctionOpeningBrace) {
                $closingBraceClass = TUseFunctionClosingBrace::class;
            } elseif ($openingBrace instanceof TUseConstOpeningBrace) {
                $closingBraceClass = TUseConstClosingBrace::class;
            } else {
                $closingBraceClass = TUseClosingBrace::class;
            }

            // back-track to find TUse/TUseFunction/TUseConst and collect prefix
            $usePos = null;
            $prefix = '';

            for ($b = $openingBracePos - 1; $b >= 0; $b --) {
                $backToken = $result[$b] ?? null;

                if ($backToken === null) {
                    break;
                }

                if (
                    $backToken instanceof TUse
                    || $backToken instanceof TUseFunction
                    || $backToken instanceof TUseConst
                ) {
                    $usePos = $b;
                    break;
                }
            }

            if ($usePos === null) {
                $result[] = $token;
                $i ++;
                continue;
            }

            // collect the prefix text (everything between TUse and TUseOpeningBrace,
            // excluding TUse, TSpace, TSplit, and TNamespaceSeparator at the end)
            $prefixParts = [];

            for ($b = $usePos + 1; $b < count($result); $b ++) {
                $backToken = $result[$b];

                if ($backToken instanceof TSpace || $backToken instanceof TSplit) {
                    continue;
                }

                if ($backToken instanceof TNamespaceSeparator) {
                    // this separator is between the qualified name and the brace
                    // if it's the last non-space before the brace, skip it
                    continue;
                }

                if (
                    $backToken instanceof TQualifiedName
                    || $backToken instanceof TUnqualifiedName
                ) {
                    $prefixParts[] = $backToken->text;
                }
            }

            $prefix = implode('\\', $prefixParts);

            if ($prefix !== '') {
                $prefix .= '\\';
            }

            // save the TUse token
            $useToken = $result[$usePos];

            // remove everything from usePos to end of result (we'll rebuild)
            $result = array_slice($result, 0, $usePos);

            // find closing brace and semicolon
            $closingBracePos = null;
            $semicolonPos = null;

            for ($j = $openingBracePos + 1; $j < $count; $j ++) {
                if ($tokens[$j] instanceof $closingBraceClass) {
                    $closingBracePos = $j;
                    break;
                }
            }

            if ($closingBracePos === null) {
                // malformed, restore and continue
                for ($b = $usePos; $b <= $openingBracePos; $b ++) {
                    if ($b < $openingBracePos) {
                        $result[] = $tokens[$b] ?? $token; // approximate
                    }
                }

                $result[] = $token;
                $i ++;
                continue;
            }

            // find the semicolon after the closing brace
            for ($j = $closingBracePos + 1; $j < $count; $j ++) {
                if ($tokens[$j] instanceof TUseEndSemicolon) {
                    $semicolonPos = $j;
                    break;
                }

                if (
                    ! $tokens[$j] instanceof TSpace && ! $tokens[$j] instanceof TSplit
                ) {
                    break;
                }
            }

            if ($semicolonPos === null) {
                $semicolonPos = $closingBracePos; // fallback
            }

            // collect segments from inside the braces
            $segments = [];
            $currentSegment = [];

            for ($j = $openingBracePos + 1; $j < $closingBracePos; $j ++) {
                $innerToken = $tokens[$j];

                if ($innerToken instanceof TUseComma) {
                    if ($currentSegment !== []) {
                        $segments[] = $currentSegment;
                        $currentSegment = [];
                    }

                    continue;
                }

                if ($innerToken instanceof TSpace || $innerToken instanceof TSplit) {
                    continue;
                }

                $currentSegment[] = $innerToken;
            }

            if ($currentSegment !== []) {
                $segments[] = $currentSegment;
            }

            // build expanded statements
            $line = $useToken->line;
            $pos = $useToken->pos;
            $first = true;

            foreach ($segments as $segment) {
                if (! $first) {
                    $result[] = new TLineBreak(T::SYNTHETIC, '', $line, $pos);
                }

                $first = false;

                // TUse (or TUseFunction/TUseConst)
                $newUse = new (get_class($useToken))(T_USE, 'use', $line, $pos);
                $result[] = $newUse;
                $result[] = new TSpace(T::SYNTHETIC, ' ', $line, $pos);

                // for TUseFunction, add "function " keyword
                if ($useToken instanceof TUseFunction) {
                    $result[] = new TUseFunction(T_FUNCTION, 'function', $line, $pos);
                    $result[] = new TSpace(T::SYNTHETIC, ' ', $line, $pos);
                }

                // for TUseConst, add "const " keyword
                if ($useToken instanceof TUseConst) {
                    $result[] = new TUseConst(T_CONST, 'const', $line, $pos);
                    $result[] = new TSpace(T::SYNTHETIC, ' ', $line, $pos);
                }

                // build the fully qualified name
                $segmentName = '';

                foreach ($segment as $segToken) {
                    if ($segToken instanceof TUseAs || $segToken instanceof TUseAlias) {
                        break;
                    }

                    $segmentName .= $segToken->text;
                }

                $fullName = $prefix . $segmentName;
                $result[] = new TQualifiedName(
                    T_NAME_QUALIFIED,
                    $fullName,
                    $line,
                    $pos,
                );

                // check for alias
                foreach ($segment as $idx => $segToken) {
                    if ($segToken instanceof TUseAs) {
                        $result[] = new TSpace(T::SYNTHETIC, ' ', $line, $pos);
                        $result[] = new TUseAs(T_AS, 'as', $line, $pos);

                        // find the alias token after TUseAs
                        for ($a = $idx + 1; $a < count($segment); $a ++) {
                            if ($segment[$a] instanceof TUseAlias) {
                                $result[] = new TSpace(T::SYNTHETIC, ' ', $line, $pos);
                                $result[] = new TUseAlias(
                                    T_STRING,
                                    $segment[$a]->text,
                                    $line,
                                    $pos,
                                );
                                break;
                            }
                        }

                        break;
                    }
                }

                $result[] = new TUseEndSemicolon(ord(';'), ';', $line, $pos);
            }

            // skip past the semicolon
            $i = $semicolonPos + 1;
        }

        return $result;
    }
}
