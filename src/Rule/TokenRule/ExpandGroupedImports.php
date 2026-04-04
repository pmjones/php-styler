<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\AUseGroupCloser;
use PhpStyler\Token\AUseGroupOpener;
use PhpStyler\Token\TConstName;
use PhpStyler\Token\TFunctionName;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TNamespaceSeparator;
use PhpStyler\Token\TQualifiedName;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TUse;
use PhpStyler\Token\TUseAlias;
use PhpStyler\Token\TUseAs;
use PhpStyler\Token\TUseComma;
use PhpStyler\Token\TUseConst;
use PhpStyler\Token\TUseEndSemicolon;
use PhpStyler\Token\TUseFunction;

class ExpandGroupedImports extends ATokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if (! ($token instanceof TUse)) {
                $result[] = $token;
                continue;
            }

            // scan forward from TUse, collecting prefix and looking for brace
            $prefixParts = [];
            $keyword = null;
            $braceIndex = null;

            for ($j = $i + 1; $j < $count; $j ++) {
                $t = $tokens[$j];

                if ($t instanceof AUseGroupOpener) {
                    $braceIndex = $j;
                    break;
                }

                if ($t instanceof TUseEndSemicolon) {
                    break; // normal import, not grouped
                }

                if ($t instanceof TUseFunction) {
                    $keyword = TUseFunction::class;
                } elseif ($t instanceof TUseConst) {
                    $keyword = TUseConst::class;
                } elseif ($this->isNameToken($t)) {
                    $prefixParts[] = $t->text;
                } elseif ($t instanceof TNamespaceSeparator) {
                    $prefixParts[] = '\\';
                }
            }

            if ($braceIndex === null) {
                $result[] = $token;
                continue;
            }

            $prefix = rtrim(implode('', $prefixParts), '\\');

            // find closing brace and semicolon in one pass
            $closeBraceIndex = null;
            $semicolonIndex = null;

            for ($j = $braceIndex + 1; $j < $count; $j ++) {
                $t = $tokens[$j];

                if ($closeBraceIndex === null && $t instanceof AUseGroupCloser) {
                    $closeBraceIndex = $j;
                    continue;
                }

                if ($closeBraceIndex !== null && ! $t->isIgnorable()) {
                    if ($t instanceof TUseEndSemicolon) {
                        $semicolonIndex = $j;
                    }

                    break;
                }
            }

            if ($closeBraceIndex === null || $semicolonIndex === null) {
                $result[] = $token;
                continue;
            }

            // parse segments from inside braces
            $segments = $this->parseSegments(
                $tokens,
                $braceIndex + 1,
                $closeBraceIndex,
            );

            if ($segments === []) {
                $result[] = $token;
                continue;
            }

            // emit individual use statements
            foreach ($segments as $idx => $segment) {
                if ($idx > 0) {
                    $result[] = new TLineBreak(AToken::SYNTHETIC, '');
                }

                $result[] = new TUse(AToken::SYNTHETIC, 'use');
                $result[] = new TSpace(AToken::SYNTHETIC, ' ');

                $effectiveKeyword = $segment['keyword'] ?? $keyword;

                if ($effectiveKeyword === TUseFunction::class) {
                    $result[] = new TUseFunction(AToken::SYNTHETIC, 'function');
                    $result[] = new TSpace(AToken::SYNTHETIC, ' ');
                } elseif ($effectiveKeyword === TUseConst::class) {
                    $result[] = new TUseConst(AToken::SYNTHETIC, 'const');
                    $result[] = new TSpace(AToken::SYNTHETIC, ' ');
                }

                $result[] = new TQualifiedName(
                    AToken::SYNTHETIC,
                    $prefix . '\\' . $segment['name'],
                );

                if ($segment['alias'] !== null) {
                    $result[] = new TSpace(AToken::SYNTHETIC, ' ');
                    $result[] = new TUseAs(AToken::SYNTHETIC, 'as');
                    $result[] = new TSpace(AToken::SYNTHETIC, ' ');
                    $result[] = new TUseAlias(AToken::SYNTHETIC, $segment['alias']);
                }

                $result[] = new TUseEndSemicolon(AToken::SYNTHETIC, ';');
            }

            $i = $semicolonIndex;
        }

        return $result;
    }

    /**
     * Parse segments from inside the braces of a grouped import.
     *
     * @param AToken[] $tokens
     * @return list<array{keyword: ?string, name: string, alias: ?string}>
     */
    private function parseSegments(array $tokens, int $from, int $to) : array
    {
        $segments = [];
        $nameParts = [];
        $alias = null;
        $keyword = null;
        $inAs = false;

        for ($j = $from; $j < $to; $j ++) {
            $t = $tokens[$j];

            if ($t instanceof TUseComma) {
                $this->flushSegment($segments, $nameParts, $keyword, $alias);
                $nameParts = [];
                $alias = null;
                $keyword = null;
                $inAs = false;
                continue;
            }

            if ($t->isIgnorable()) {
                continue;
            }

            if ($t instanceof TUseAs) {
                $inAs = true;
            } elseif ($t instanceof TUseFunction) {
                $keyword = TUseFunction::class;
            } elseif ($t instanceof TUseConst) {
                $keyword = TUseConst::class;
            } elseif (
                $inAs && ($t instanceof TUseAlias || $this->isNameToken($t))
            ) {
                $alias = $t->text;
            } elseif ($this->isNameToken($t) || $t instanceof TNamespaceSeparator) {
                $nameParts[] = $t->text;
            }
        }

        $this->flushSegment($segments, $nameParts, $keyword, $alias);

        return $segments;
    }

    /**
     * @param list<array{keyword: ?string, name: string, alias: ?string}> $segments
     * @param string[] $nameParts
     */
    private function flushSegment(
        array &$segments,
        array $nameParts,
        ?string $keyword,
        ?string $alias,
    ) : void
    {
        $name = implode('', $nameParts);

        if ($name !== '') {
            $segments[] = [
                'keyword' => $keyword,
                'name' => $name,
                'alias' => $alias,
            ];
        }
    }

    private function isNameToken(AToken $token) : bool
    {
        return $token->is([
            T_STRING,
            T_NAME_QUALIFIED,
            T_NAME_FULLY_QUALIFIED,
        ]) || $token instanceof TConstName || $token instanceof TFunctionName;
    }
}
