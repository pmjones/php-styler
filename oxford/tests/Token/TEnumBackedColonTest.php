<?php
declare(strict_types=1);

namespace Oxford\Token;

class TEnumBackedColonTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'backed-enum-string' => [
                <<<'CODE'
                <?php
                enum Suit : string {
                    case Hearts = 'hearts';
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEnum::class,
                    TEnumName::class,
                    TEnumBackedColon::class,
                    TString::class,
                    TEnumOpeningBrace::class,
                    TEnumCase::class,
                    TEnumCaseName::class,
                    TAssign::class,
                    TStringLiteral::class,
                    TEnumCaseEndSemicolon::class,
                    TEnumClosingBrace::class,
                ],
            ],
            'backed-enum-int' => [
                <<<'CODE'
                <?php
                enum Color : int {
                    case Red = 1;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEnum::class,
                    TEnumName::class,
                    TEnumBackedColon::class,
                    TInt::class,
                    TEnumOpeningBrace::class,
                    TEnumCase::class,
                    TEnumCaseName::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TEnumCaseEndSemicolon::class,
                    TEnumClosingBrace::class,
                ],
            ],
        ];
    }
}
