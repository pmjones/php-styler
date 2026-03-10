<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUnsetTest extends TTestCase
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
                unset($foo);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUnset::class,
                    TArgsOpeningParen::class,
                    TVariable::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
