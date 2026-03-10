<?php
declare(strict_types=1);

namespace Oxford\Token;

class TTryTest extends TTestCase
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
                    $foo = $bar;
                } catch (Exception $e) {
                    $baz = dib;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TTry::class,
                    TTryOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TVariable::class,
                    TSemicolon::class,
                    TTryContinuationBrace::class,
                    TCatch::class,
                    TParamsOpeningParen::class,
                    TUnqualifiedName::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TCatchOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TUnknownString::class,
                    TSemicolon::class,
                    TCatchClosingBrace::class,
                ],
            ],
            'try-catch-finally' => [
                <<<'CODE'
                <?php
                try {
                    $foo = $bar;
                } catch (Exception $e) {
                    $baz = dib;
                } finally {
                    $irk = $doom;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TTry::class,
                    TTryOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TVariable::class,
                    TSemicolon::class,
                    TTryContinuationBrace::class,
                    TCatch::class,
                    TParamsOpeningParen::class,
                    TUnqualifiedName::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TCatchOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TUnknownString::class,
                    TSemicolon::class,
                    TCatchContinuationBrace::class,
                    TFinally::class,
                    TFinallyOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TVariable::class,
                    TSemicolon::class,
                    TFinallyClosingBrace::class,
                ],
            ],
            'try-catch-catch' => [
                <<<'CODE'
                <?php
                try {
                    $foo = $bar;
                } catch (Exception $e) {
                    $baz = dib;
                } catch (Exception) {
                    $zim = $gir;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TTry::class,
                    TTryOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TVariable::class,
                    TSemicolon::class,
                    TTryContinuationBrace::class,
                    TCatch::class,
                    TParamsOpeningParen::class,
                    TUnqualifiedName::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TCatchOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TUnknownString::class,
                    TSemicolon::class,
                    TCatchContinuationBrace::class,
                    TCatch::class,
                    TParamsOpeningParen::class,
                    TUnqualifiedName::class,
                    TParamsClosingParen::class,
                    TCatchOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TVariable::class,
                    TSemicolon::class,
                    TCatchClosingBrace::class,
                ],
            ],
        ];
    }
}
