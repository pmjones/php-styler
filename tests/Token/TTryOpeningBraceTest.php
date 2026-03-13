<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TTryOpeningBraceTest extends TTestCase
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
                try {
                } catch (\Exception $e) {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TTry::class,
                    TTryOpeningBrace::class,
                    TTryContinuationBrace::class,
                    TCatch::class,
                    TParamsOpeningParen::class,
                    TFullyQualifiedName::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TCatchOpeningBrace::class,
                    TCatchClosingBrace::class,
                ],
            ],
        ];
    }
}
