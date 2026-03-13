<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TFirstClassCallableEllipsisTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'basic' => [
                <<<'CODE'
                <?php
                $foo = strlen(...);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TFunctionCallName::class,
                    TArgsOpeningParen::class,
                    TFirstClassCallableEllipsis::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
