<?php
declare(strict_types=1);

namespace Oxford\Token;

class TIfClosingParenTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'basic' => [
                <<<'CODE'
                <?php
                if ($foo) {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIfClosingParen::class,
                    TIfOpeningBrace::class,
                    TIfClosingBrace::class,
                ],
            ],
        ];
    }
}
