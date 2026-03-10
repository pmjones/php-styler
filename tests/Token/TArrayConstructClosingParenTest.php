<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TArrayConstructClosingParenTest extends TTestCase
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
                $foo = array(1, 2);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TArrayConstruct::class,
                    TArrayConstructOpeningParen::class,
                    TIntegerLiteral::class,
                    TArrayComma::class,
                    TIntegerLiteral::class,
                    TArrayConstructClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
