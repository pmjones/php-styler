<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TEvalTest extends TTestCase
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
                eval($code);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEval::class,
                    TArgsOpeningParen::class,
                    TVariable::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
