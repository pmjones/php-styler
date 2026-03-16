<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TStringFragmentTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'string-with-variable' => [
                <<<'CODE'
                <?php
                $foo = "hello $bar world";
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TDoubleQuote::class,
                    TStringFragment::class,
                    TEncapsedVariable::class,
                    TStringFragment::class,
                    TDoubleQuote::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
