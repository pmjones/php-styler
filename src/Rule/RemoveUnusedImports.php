<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TBlankLine;
use PhpStyler\Token\TConstName;
use PhpStyler\Token\TDocblock;
use PhpStyler\Token\TFunctionCallName;
use PhpStyler\Token\TFunctionCallQualified;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TQualifiedName;
use PhpStyler\Token\TUnknownString;
use PhpStyler\Token\TUnqualifiedName;
use PhpStyler\Token\TUse;
use PhpStyler\Token\TUseAlias;
use PhpStyler\Token\TUseConst;
use PhpStyler\Token\TUseEndSemicolon;
use PhpStyler\Token\TUseFunction;
use PhpStyler\Token\TWhitespaceEol;

class RemoveUnusedImports implements TokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $count = count($tokens);

        // Pass 1: collect imports

        /** @var array<int, array{start: int, end: int, localName: string}> $imports */
        $imports = [];
        $i = 0;

        while ($i < $count) {
            $token = $tokens[$i];

            if (! $this->isUseKeyword($token)) {
                $i ++;
                continue;
            }

            $start = $i;
            $localName = null;
            $aliasName = null;

            // walk through the import statement to find the local name
            while ($i < $count) {
                $token = $tokens[$i];

                if ($token instanceof TUseAlias) {
                    $aliasName = $token->text;
                }

                if (
                    $token instanceof TQualifiedName
                    || $token instanceof TUnqualifiedName
                ) {
                    // last segment of qualified name is the local name
                    $parts = explode('\\', $token->text);
                    $localName = end($parts);
                }

                if ($token instanceof TUseEndSemicolon) {
                    // consume trailing separators
                    $end = $i;
                    $j = $i + 1;

                    while ($j < $count && $this->isSeparator($tokens[$j])) {
                        // only consume if the token after the separator run is
                        // NOT another use keyword (we'll let that import consume
                        // its own leading context)
                        $end = $j;
                        $j ++;
                    }

                    // if the next non-separator is another use statement, consume
                    // trailing separators; otherwise consume them too (to remove
                    // the blank line left behind)
                    $effectiveName = $aliasName ?? $localName;

                    if ($effectiveName !== null) {
                        $imports[] = [
                            'start' => $start,
                            'end' => $end,
                            'localName' => $effectiveName,
                        ];
                    }

                    $i = $end + 1;
                    break;
                }

                $i ++;
            }
        }

        if ($imports === []) {
            return $tokens;
        }

        // Pass 1b: collect used names from code and docblocks

        /** @var array<string, true> $usedNames */
        $usedNames = [];

        // build a set of import ranges to skip when scanning for usage
        $importRanges = [];

        foreach ($imports as $import) {
            for ($r = $import['start']; $r <= $import['end']; $r ++) {
                $importRanges[$r] = true;
            }
        }

        for ($i = 0; $i < $count; $i ++) {
            // skip tokens that are part of import statements
            if (isset($importRanges[$i])) {
                continue;
            }

            $token = $tokens[$i];

            // code name references
            if (
                $token instanceof TUnqualifiedName
                || $token instanceof TFunctionCallName
                || $token instanceof TConstName
                || $token instanceof TUnknownString
            ) {
                $usedNames[$token->text] = true;
            }

            // qualified names: first segment may match an import
            if (
                $token instanceof TQualifiedName
                || $token instanceof TFunctionCallQualified
            ) {
                $parts = explode('\\', $token->text);
                $usedNames[$parts[0]] = true;
            }

            // docblock references
            if ($token instanceof TDocblock) {
                $docblock = $token->getDocblock();

                foreach ($docblock->tags as $tag) {
                    $this->extractDocblockNames($tag->body, $usedNames);
                }
            }
        }

        // Pass 2: rebuild, skipping unused imports
        $skipRanges = [];

        foreach ($imports as $import) {
            if (! isset($usedNames[$import['localName']])) {
                for ($r = $import['start']; $r <= $import['end']; $r ++) {
                    $skipRanges[$r] = true;
                }
            }
        }

        if ($skipRanges === []) {
            return $tokens;
        }

        $result = [];

        for ($i = 0; $i < $count; $i ++) {
            if (! isset($skipRanges[$i])) {
                $result[] = $tokens[$i];
            }
        }

        return $result;
    }

    private function isUseKeyword(AToken $token) : bool
    {
        return $token instanceof TUse
            || $token instanceof TUseConst
            || $token instanceof TUseFunction;
    }

    private function isSeparator(AToken $token) : bool
    {
        return $token instanceof TWhitespaceEol
            || $token instanceof TLineBreak
            || $token instanceof TBlankLine;
    }

    /**
     * @param array<string, true> $usedNames
     */
    private function extractDocblockNames(string $body, array &$usedNames) : void
    {
        // split on type-expression delimiters
        $segments = preg_split('/[\s|&<>(),{}\[\]]/', $body);

        if ($segments === false) {
            return;
        }

        foreach ($segments as $segment) {
            $segment = ltrim($segment, '?');
            $segment = rtrim($segment, '.');

            if ($segment !== '' && $segment[0] !== '$') {
                $usedNames[$segment] = true;
            }
        }
    }
}
