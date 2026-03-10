<?php
declare(strict_types=1);

namespace Oxford\Token;

class TCatchTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'try-catch' => [
                <<<'CODE'
                <?php
                try {
                } catch (Exception $e) {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TTry::class,
                    TTryOpeningBrace::class,
                    TTryContinuationBrace::class,
                    TCatch::class,
                    TParamsOpeningParen::class,
                    TUnqualifiedName::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TCatchOpeningBrace::class,
                    TCatchClosingBrace::class,
                ],
            ],
        ];
    }
}
