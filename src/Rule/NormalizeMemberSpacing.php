<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TAbstractMethodEndSemicolon;
use PhpStyler\Token\TBlankLine;
use PhpStyler\Token\TConstEndSemicolon;
use PhpStyler\Token\TEnumCaseEndSemicolon;
use PhpStyler\Token\TFunctionClosingBrace;
use PhpStyler\Token\TPropertyEndSemicolon;
use PhpStyler\Token\TPropertyHooksClosingBrace;
use PhpStyler\Token\TUseTraitClosingBrace;
use PhpStyler\Token\TUseTraitEndSemicolon;

class NormalizeMemberSpacing implements TokenRule
{
    public function __construct(
        private int $betweenConstants = 0,
        private int $betweenProperties = 0,
        private int $betweenEnumCases = 0,
        private int $betweenTraitUses = 0,
        private int $betweenMethods = 1,
    ) {
    }

    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        // Phase 1: collect member ending positions and types
        $memberEndings = [];

        foreach ($tokens as $i => $token) {
            $type = $this->memberType($token);

            if ($type !== null) {
                $memberEndings[] = ['pos' => $i, 'type' => $type];
            }
        }

        // Phase 2: for consecutive same-type endings, mark blank lines for removal
        $removeBlankLines = [];

        for ($idx = 0; $idx < count($memberEndings) - 1; $idx ++) {
            $current = $memberEndings[$idx];
            $next = $memberEndings[$idx + 1];
            $desiredBlanks = $this->desiredBlanks($current['type'], $next['type']);

            if ($desiredBlanks >= 1) {
                continue;
            }

            // Find and mark blank lines between these two member endings
            for ($i = $current['pos'] + 1; $i < $next['pos']; $i ++) {
                if ($tokens[$i] instanceof TBlankLine) {
                    $removeBlankLines[$i] = true;
                }
            }
        }

        // Phase 3: rebuild token array, removing marked blank lines
        $result = [];

        foreach ($tokens as $i => $token) {
            if (isset($removeBlankLines[$i])) {
                continue;
            }

            $result[] = $token;
        }

        return $result;
    }

    private function memberType(AToken $token) : ?string
    {
        return match (true) {
            $token instanceof TConstEndSemicolon => 'const',

            $token instanceof TPropertyEndSemicolon,
            $token instanceof TPropertyHooksClosingBrace => 'property',

            $token instanceof TEnumCaseEndSemicolon => 'enumcase',

            $token instanceof TUseTraitEndSemicolon,
            $token instanceof TUseTraitClosingBrace => 'traituse',
            $token instanceof TFunctionClosingBrace,
            $token instanceof TAbstractMethodEndSemicolon => 'method',

            default => null,
        };
    }

    private function desiredBlanks(string $currentType, string $nextType) : int
    {
        if ($currentType !== $nextType) {
            return 1;
        }

        return match ($currentType) {
            'const' => $this->betweenConstants,
            'property' => $this->betweenProperties,
            'enumcase' => $this->betweenEnumCases,
            'traituse' => $this->betweenTraitUses,
            'method' => $this->betweenMethods,
            default => 1,
        };
    }
}
