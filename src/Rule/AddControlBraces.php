<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TElse;
use PhpStyler\Token\TElseClosingBrace;
use PhpStyler\Token\TElseClosingBraceless;
use PhpStyler\Token\TElseifClosingBrace;
use PhpStyler\Token\TElseifClosingBraceless;
use PhpStyler\Token\TElseifClosingParen;
use PhpStyler\Token\TElseifContinuationBrace;
use PhpStyler\Token\TElseifContinuationBraceless;
use PhpStyler\Token\TElseifOpeningBrace;
use PhpStyler\Token\TElseOpeningBrace;
use PhpStyler\Token\TForClosingBrace;
use PhpStyler\Token\TForClosingBraceless;
use PhpStyler\Token\TForClosingParen;
use PhpStyler\Token\TForeachClosingBrace;
use PhpStyler\Token\TForeachClosingBraceless;
use PhpStyler\Token\TForeachClosingParen;
use PhpStyler\Token\TForeachOpeningBrace;
use PhpStyler\Token\TForOpeningBrace;
use PhpStyler\Token\TIfClosingBrace;
use PhpStyler\Token\TIfClosingBraceless;
use PhpStyler\Token\TIfClosingParen;
use PhpStyler\Token\TIfContinuationBrace;
use PhpStyler\Token\TIfContinuationBraceless;
use PhpStyler\Token\TIfOpeningBrace;
use PhpStyler\Token\TOpeningBraceless;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TWhileClosingBrace;
use PhpStyler\Token\TWhileClosingBraceless;
use PhpStyler\Token\TWhileClosingParen;
use PhpStyler\Token\TWhileOpeningBrace;

class AddControlBraces implements TokenRule
{
    protected const OPENING_BRACE_MAP = [
        TIfClosingParen::class => TIfOpeningBrace::class,
        TElseifClosingParen::class => TElseifOpeningBrace::class,
        TElse::class => TElseOpeningBrace::class,
        TForClosingParen::class => TForOpeningBrace::class,
        TForeachClosingParen::class => TForeachOpeningBrace::class,
        TWhileClosingParen::class => TWhileOpeningBrace::class,
    ];
    protected const CLOSING_BRACE_MAP = [
        TIfClosingBraceless::class => TIfClosingBrace::class,
        TElseClosingBraceless::class => TElseClosingBrace::class,
        TElseifClosingBraceless::class => TElseifClosingBrace::class,
        TForClosingBraceless::class => TForClosingBrace::class,
        TForeachClosingBraceless::class => TForeachClosingBrace::class,
        TWhileClosingBraceless::class => TWhileClosingBrace::class,
    ];
    protected const CONTINUATION_BRACE_MAP = [
        TIfContinuationBraceless::class => TIfContinuationBrace::class,
        TElseifContinuationBraceless::class => TElseifContinuationBrace::class,
    ];

    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];

        foreach ($tokens as $token) {
            if ($token instanceof TOpeningBraceless) {
                $result[] = $this->replaceOpeningBraceless($token, $result);
                continue;
            }

            $class = get_class($token);

            if (isset(self::CLOSING_BRACE_MAP[$class])) {
                $braceClass = self::CLOSING_BRACE_MAP[$class];
                $result[] = new $braceClass(
                    AToken::SYNTHETIC,
                    '}',
                    $token->line,
                    $token->pos,
                );
                continue;
            }

            if (isset(self::CONTINUATION_BRACE_MAP[$class])) {
                $braceClass = self::CONTINUATION_BRACE_MAP[$class];
                $result[] = new $braceClass(
                    AToken::SYNTHETIC,
                    '}',
                    $token->line,
                    $token->pos,
                );
                continue;
            }

            $result[] = $token;
        }

        return $result;
    }

    /**
     * @param AToken[] $result
     */
    protected function replaceOpeningBraceless(
        TOpeningBraceless $token,
        array $result,
    ) : AToken
    {
        for ($i = count($result) - 1; $i >= 0; $i --) {
            if ($result[$i] instanceof TSpace) {
                continue;
            }

            $class = get_class($result[$i]);

            if (isset(self::OPENING_BRACE_MAP[$class])) {
                $braceClass = self::OPENING_BRACE_MAP[$class];
                return new $braceClass(
                    AToken::SYNTHETIC,
                    '{',
                    $token->line,
                    $token->pos,
                );
            }

            break;
        }

        return $token;
    }
}
