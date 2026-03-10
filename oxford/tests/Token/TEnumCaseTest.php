<?php
declare(strict_types=1);

namespace Oxford\Token;

class TEnumCaseTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'basic-enum-case' => [
                <<<'CODE'
                <?php
                enum Color {
                    case Red;
                    case Blue;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEnum::class,
                    TEnumName::class,
                    TEnumOpeningBrace::class,
                    TEnumCase::class,
                    TEnumCaseName::class,
                    TEnumCaseEndSemicolon::class,
                    TEnumCase::class,
                    TEnumCaseName::class,
                    TEnumCaseEndSemicolon::class,
                    TEnumClosingBrace::class,
                ],
            ],
            'backed-enum-case' => [
                <<<'CODE'
                <?php
                enum Suit : string {
                    case Hearts = 'hearts';
                    case Spades = 'spades';
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
                    TEnumCase::class,
                    TEnumCaseName::class,
                    TAssign::class,
                    TStringLiteral::class,
                    TEnumCaseEndSemicolon::class,
                    TEnumClosingBrace::class,
                ],
            ],
        ];
    }
}
